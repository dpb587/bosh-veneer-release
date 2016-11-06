<?php

namespace Veneer\BoshEditorBundle\Form\Type\CloudConfig;

use Symfony\Component\Form\FormBuilderInterface;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class NetworkVipType extends AbstractDeploymentManifestPathType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'cloud_properties',
                $options['cpi']->getNetworkVipForm(),
                [
                    'label' => 'Cloud Properties',
                    'veneer_help_html' => '<p>IaaS-specific properties for the network.</p>',
                ]
            )
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setRequired([
            'cpi',
        ]);
    }

    public function getName()
    {
        return 'veneer_bosh_editor_editor_cloudconfig_network_vip';
    }
}
