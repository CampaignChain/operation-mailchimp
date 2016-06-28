<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) CampaignChain, Inc. <info@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
            'empty_value' => true,
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
