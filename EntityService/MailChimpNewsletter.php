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

namespace CampaignChain\Operation\MailChimpBundle\EntityService;

use Doctrine\Common\Persistence\ManagerRegistry;
use CampaignChain\CoreBundle\EntityService\OperationServiceInterface;
use CampaignChain\CoreBundle\Entity\Operation;

class MailChimpNewsletter implements OperationServiceInterface
{
    protected $em;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->em = $managerRegistry->getManager();
    }

    public function getContent(Operation $operation)
    {
        return $this->getNewsletterByOperation($operation->getId());
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     * @deprecated Use getContent(Operation $operation) instead.
     */
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
        $newsletter = $this->getNewsletterByOperation($oldOperation->getId());
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
