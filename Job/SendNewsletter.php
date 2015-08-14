<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) Sandro Groganz <sandro@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Operation\MailChimpBundle\Job;

use CampaignChain\CoreBundle\Entity\Action;
use Doctrine\ORM\EntityManager;
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

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
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
        $ctaNewsletterContent = $ctaService->processCTAs(
                                    $newsletterContent['html'],
                                    $localNewsletter->getOperation(),
                                    CTAService::FORMAT_HTML
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
            throw new \Exception(print_r($readyResponse['items'], true));
        }
    }
}