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
        $restService = $this->container->get('campaignchain.channel.mailchimp.rest.client');
        $client = $restService->connectByActivity($localNewsletter->getOperation()->getActivity());

        /*
         * Get remote newsletter data.
         */

        $remoteNewsletterData = $client->campaigns->getList(array(
            'campaign_id' => $localNewsletter->getCampaignId(),
        ));

        $remoteNewsletter = $remoteNewsletterData['data'][0];

        /*
         * Grab the raw newsletter content and parse all included URLs to see
         * if they are pointing to Channels connected with CampaignChain. If so,
         * then add a tracking ID.
         */

        // Get the newsletter content in raw HTML format.
        $newsletterContent = $client->campaigns->content(
            $localNewsletter->getCampaignId(),
            array(
                'view' => 'raw',
            )
        );

        // Set template_id to null to be able to paste parsed HTML.
        if($remoteNewsletter['template_id'] != 0){
            $client->campaigns->update(
                $localNewsletter->getCampaignId(),
                'options',
                array(
                    'template_id' => null,
                )
            );
        }

        // Process URLs in message and save the new message text, now including
        // the replaced URLs with the Tracking ID attached for call to action tracking.
        $ctaService = $this->container->get('campaignchain.core.cta');
        $options['format'] = CTAService::FORMAT_HTML;
        $ctaNewsletterContent = $ctaService->processCTAs(
                                    $newsletterContent['html'],
                                    $localNewsletter->getOperation(),
                                    $options
                                )->getContent();

        $updatedNewsletter = $client->campaigns->update(
            $localNewsletter->getCampaignId(),
            'content',
            array(
                'html' => $ctaNewsletterContent,
            )
        );

        // Check if newsletter is ready to be sent.
        $readyResponse = $client->campaigns->ready($localNewsletter->getCampaignId());

        if($readyResponse['is_ready']) {
            // Send the newsletter.
            $client->campaigns->send($localNewsletter->getCampaignId());

            // Update local newsletter data.
            $localNewsletter->setContentUpdatedTime(
                new \DateTime($updatedNewsletter['data']['content_updated_time'])
            );

            $localNewsletter->getOperation()->setStatus(Action::STATUS_CLOSED);

            // Schedule data collection for report
            $report = $this->container->get('campaignchain.job.report.mailchimp.newsletter');
            $report->schedule($localNewsletter->getOperation());

            $this->em->flush();

            $this->message = 'Successfully sent the MailChimp newsletter campaign with the title "'.
                $remoteNewsletter['title'].'" and subject "'.
                $remoteNewsletter['subject'].'". View it here: '.$remoteNewsletter['archive_url_long'];

            return self::STATUS_OK;
        } else {
            // Find the error.
            foreach($readyResponse['items'] as $values){
                if($values['type'] == 'cross-large'){
                    throw new ExternalApiException($values['heading'].': '.$values['details']);
                }
            }

            throw new ExternalApiException('Unknown error when sending MailChimp newsletter.');
        }
    }
}