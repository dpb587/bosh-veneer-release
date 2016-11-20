<?php

namespace Veneer\BoshEditorBundle\Form\Type\CloudConfig;

use Symfony\Component\Form\FormBuilderInterface;
use Veneer\BoshEditorBundle\Form\DataTransformer\DeploymentNetworkTransformer;
use Veneer\BoshEditorBundle\Form\Type\AbstractDeploymentManifestPathType;

class NetworkType extends AbstractDeploymentManifestPathType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                [
                    'label' => 'Network Name',
                    'veneer_help_html' => '<p>DNS IP addresses for this network</p>',
                    'required' => false,
                ]
            )
            ->add(
                'network',
                'veneer_core_form_picker',
                [
                    'label' => 'Network Type',
                    'forms' => [
                        'manual' => [
                            'veneer_bosh_editor_editor_cloudconfig_network_manual',
                            [
                                'label' => 'Manual',
                                'manifest' => $options['manifest'],
                                'manifest_file' => $options['manifest_file'],
                            ],
                        ],
                        'dynamic' => [
                            'veneer_bosh_editor_editor_cloudconfig_network_dynamic',
                            [
                                'label' => 'Dynamic',
                                'manifest' => $options['manifest'],
                                'manifest_file' => $options['manifest_file'],
                            ],
                        ],
                    ],
                ]
            )
        ;

        $builder->addModelTransformer(new DeploymentNetworkTransformer());
    }

    public function getName()
    {
        return 'veneer_bosh_editor_editor_cloudconfig_network';
    }
}
