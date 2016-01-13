<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) CampaignChain, Inc. <info@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Operation\MailChimpBundle\EntityService;

use Doctrine\ORM\EntityManager;
use CampaignChain\CoreBundle\EntityService\OperationServiceInterface;
use CampaignChain\CoreBundle\Entity\Operation;

class MailChimpNewsletter implements OperationServiceInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getNewsletterByOperation($id){
        $newsletter = $this->em->getRepository('CampaignChainOperationMailChimpBundle:MailChimpNewsletter')
            ->findOneByOperation($id);

        if (!$newsletter) {
            throw new \Exception(
                'No newsletter found by operation id '.$id
            );
        }

        return $newsletter;
    }

    public function cloneOperation(Operation $oldOperation, Operation $newOperation)
    {
        $newsletter = $this->getNewsletterByOperation($oldOperation);
        $clonedNewsletter = clone $newsletter;
        $clonedNewsletter->setOperation($newOperation);
        $this->em->persist($clonedNewsletter);
        $this->em->flush();
    }
    public function removeOperation($id){
        try {
            $operation = $this->getNewsletterByOperation($id);
            $this->em->remove($operation);
            $this->em->flush();
        } catch (\Exception $e) {

        }
    }
}