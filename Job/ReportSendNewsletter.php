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

use CampaignChain\CoreBundle\Entity\SchedulerReportOperation;
use CampaignChain\CoreBundle\Job\JobReportInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ReportSendNewsletter implements JobReportInterface
{
    const OPERATION_BUNDLE_NAME = 'campaignchain/operation-mailchimp';
    const METRIC_UNSUBSCRIBES = 'Unsubscribes';
    const METRIC_OPENS = 'Opens';
    const METRIC_UNIQUE_OPENS = 'Unique opens';
    const METRIC_CLICKS = 'Clicks';
    const METRIC_UNIQUE_CLICKS = 'Unique clicks';
    const METRIC_EMAILS_SENT = 'Emails_sent';
    const METRIC_USERS_WHO_CLICKED = 'Users who clicked';

    protected $em;
    protected $container;
    protected $message;
    protected $operation;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getMessage(){
        return $this->message;
    }
    
    public function schedule($operation, $facts = null)
    {
        $scheduler = new SchedulerReportOperation();
        $scheduler->setOperation($operation);
        $scheduler->setInterval('1 hour');
        $scheduler->setEndAction($operation->getActivity()->getCampaign());
        $this->em->persist($scheduler);

        $facts[self::METRIC_UNSUBSCRIBES]       = 0;
        $facts[self::METRIC_OPENS]              = 0;
        $facts[self::METRIC_UNIQUE_OPENS]       = 0;
        $facts[self::METRIC_CLICKS]             = 0;
        $facts[self::METRIC_UNIQUE_CLICKS]      = 0;
        $facts[self::METRIC_EMAILS_SENT]        = 0;
        $facts[self::METRIC_USERS_WHO_CLICKED]  = 0;

        $factService = $this->container->get('campaignchain.core.fact');
        $factService->addFacts('activity', self::OPERATION_BUNDLE_NAME, $operation, $facts);
    }

    public function execute($operationId)
    {
        $operationService = $this->container->get('campaignchain.core.operation');
        $operation = $operationService->getOperation($operationId);

        $newsletterService = $this->container->get('campaignchain.operation.mailchimp.newsletter');
        $newsletter = $newsletterService->getNewsletterByOperation($operation);

        $restService = $this->container->get('campaignchain.channel.mailchimp.rest.client');
        $client = $restService->connectByActivity($operation->getActivity());

        $reportSummary = $client->reports->summary($newsletter->getCampaignId());

        // Add report data.
        $facts[self::METRIC_UNSUBSCRIBES]       = $reportSummary['unsubscribes'];
        $facts[self::METRIC_OPENS]              = $reportSummary['opens'];
        $facts[self::METRIC_UNIQUE_OPENS]       = $reportSummary['unique_opens'];
        $facts[self::METRIC_CLICKS]             = $reportSummary['clicks'];
        $facts[self::METRIC_UNIQUE_CLICKS]      = $reportSummary['unique_clicks'];
        $facts[self::METRIC_EMAILS_SENT]        = $reportSummary['emails_sent'];
        $facts[self::METRIC_USERS_WHO_CLICKED]  = $reportSummary['users_who_clicked'];

        $factService = $this->container->get('campaignchain.core.fact');
        $factService->addFacts('activity', self::OPERATION_BUNDLE_NAME, $operation, $facts);

        $this->message = 'Added to report: '.
            'unsubscribes = '.$reportSummary['unsubscribes'].', '.
            'opens = '.$reportSummary['opens'].', '.
            'unique opens = '.$reportSummary['unique_opens'].', '.
            'clicks = '.$reportSummary['clicks'].', '.
            'unique clicks = '.$reportSummary['unique_clicks'].', '.
            'emails sent = '.$reportSummary['emails_sent'].', '.
            'users who clicked = '.$reportSummary['users_who_clicked'].'.';

        return self::STATUS_OK;
    }
}