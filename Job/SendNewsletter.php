<?php
/*
 * Copyright 2016 CampaignChain, Inc. <info@campaignchain.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CampaignChain\Operation\MailChimpBundle\Job;

use CampaignChain\Channel\MailChimpBundle\REST\MailChimpClient;
use CampaignChain\CoreBundle\Entity\Action;
use CampaignChain\CoreBundle\Exception\ExternalApiException;
use Doctrine\Common\Persistence\ManagerRegistry;
use CampaignChain\CoreBundle\Entity\Medium;
use CampaignChain\CoreBundle\Job\JobActionInterface;
use Symfony\Component\HttpFoundation\Response;
use CampaignChain\CoreBundle\EntityService\CTAService;

class SendNewsletter implements JobActionInterface
{
    protected $em;
    protected $container;
    protected $message;
    protected $operation;
    protected $url;

    public function __construct(ManagerRegistry $managerRegistry, $container)
    {
        $this->em = $managerRegistry->getManager();
        $this->container = $container;
    }

    public function getMessage(){
        return $this->message;
    }
    
    public function execute($operationId)
    {
        $localNewsletter = $this->em
          ->getRepository('CampaignChainOperationMailChimpBundle:MailChimpNewsletter')
          ->findOneByOperation($operationId);

        if (!$localNewsletter) {
            throw new \Exception(
                'No newsletter found for an operation with ID: '.$operationId
            );
        }

        // Connect to MailChimp REST API.
        /** @var MailChimpClient $restService */
        $restService = $this->container->get('campaignchain.channel.mailchimp.rest.client');
        $client = $restService->connectByActivity($localNewsletter->getOperation()->getActivity());

        /*
         * Get remote newsletter data.
         */
        $remoteNewsletter = $client->getCampaign($localNewsletter->getCampaignId());

        /*
         * Grab the raw newsletter content and parse all included URLs to see
         * if they are pointing to Channels connected with CampaignChain. If so,
         * then add a tracking ID.
         */

        // Get the newsletter content in raw HTML format.
        $newsletterContent = $client->getCampaignHTML($localNewsletter->getCampaignId());

        // Set template_id to null to be able to paste parsed HTML.
        if($remoteNewsletter['settings']['template_id'] != 0){
            $client->resetCampaignTemplate($localNewsletter->getCampaignId());
        }

        // Process URLs in message and save the new message text, now including
        // the replaced URLs with the Tracking ID attached for call to action tracking.
        $ctaService = $this->container->get('campaignchain.core.cta');
        $options['format'] = CTAService::FORMAT_HTML;
        $ctaNewsletterContent = $ctaService->processCTAs(
                                    $newsletterContent,
                                    $localNewsletter->getOperation(),
                                    $options
                                )->getContent();

        $updatedNewsletter = $client->updateCampaignHTML(
            $localNewsletter->getCampaignId(),
            $ctaNewsletterContent
        );

        // Catch error if MailChimp campaign was already sent.
        if(isset($updatedNewsletter['status']) && $updatedNewsletter['status'] == 400){
            $this->message = 'This MailChimp campaign was already sent.';
            return self::STATUS_WARNING;
        }

        // Check if newsletter is ready to be sent.
        $readyResponse = $client->getCampaignSendChecklist($localNewsletter->getCampaignId());

        if($readyResponse['is_ready']) {
            // Send the newsletter.
            $client->sendCampaign($localNewsletter->getCampaignId());

            // Update local newsletter data.
//            $localNewsletter->setContentUpdatedTime(
//                new \DateTime($updatedNewsletter['data']['content_updated_time'])
//            );

            $localNewsletter->getOperation()->setStatus(Action::STATUS_CLOSED);

            // Schedule data collection for report
            $report = $this->container->get('campaignchain.job.report.mailchimp.newsletter');
            $report->schedule($localNewsletter->getOperation());

            $this->em->flush();

            $this->message = 'Successfully sent the MailChimp newsletter campaign with the title "'.
                $remoteNewsletter['settings']['title'].'" and subject "'.
                $remoteNewsletter['settings']['subject_line'].'". View it here: '.$remoteNewsletter['archive_url'];

            return self::STATUS_OK;
        } else {
            // Find the error.
            foreach($readyResponse['items'] as $values){
                if($values['type'] == 'error'){
                    throw new ExternalApiException($values['heading'].': '.$values['details']);
                }
            }

            throw new ExternalApiException('Unknown error when sending MailChimp newsletter.');
        }
    }
}