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

class MailChimpReport implements JobReportInterface
{
    const OPERATION_BUNDLE_NAME = 'campaignchain/operation-mailchimp';
    const METRIC_UNSUBSCRIBES = 'Unsubscribes';
    const METRIC_OPENS = 'Opens';
    const METRIC_UNIQUE_OPENS = 'Unique_opens';
    const METRIC_CLICKS = 'Clicks';
    const METRIC_UNIQUE_CLICKS = 'Unique_clicks';
    const METRIC_EMAILS_SENT = 'Emails_sent';
    const METRIC_USERS_WHO_CLICKED = 'Users_who_clicked';

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
    }

    public function execute($operationId)
    {
    }

}