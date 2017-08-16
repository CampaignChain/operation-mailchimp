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

namespace CampaignChain\Operation\MailChimpBundle\Form\Type;

use CampaignChain\CoreBundle\Form\Type\OperationType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MailChimpOperationType
 * @package CampaignChain\Operation\MailChimpBundle\Form\Type
 */
class MailChimpOperationType extends OperationType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('newsletter', 'choice', array(
            'choices'   => $this->content,
            'placeholder' => true,
            'label' => 'Newsletter',
            'attr' => array(
                'placeholder' => 'Select a newsletter',
            ),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'campaignchain_operation_mailchimp_newsletter';
    }
}
