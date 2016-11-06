<?php

namespace Veneer\BoshEditorBundle\Form\Type\CloudConfig;

use Symfony\Component\Form\FormBuilderInterface;
use Veneer\BoshEditorBundle\Form\Type\AbstractDeploymentManifestPathType;

class NetworkManualType extends AbstractDeploymentManifestPathType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'subnets',
                'collection',
                array(
                    'label' => 'Subnets',
                    'type' => 'veneer_bosh_editor_editor_cloudconfig_network_manual_subnet',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'options' => [
                        'manifest' => $options['manifest'],
                        'manifest_path' => $options['manifest_path'],
                    ],
                )
            )
            ;
    }

    public function getName()
    {
        return 'veneer_bosh_editor_editor_cloudconfig_network_manual';
    }
}
