<?php

namespace Veneer\OpsBundle\Form\Type\CloudConfig;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Veneer\OpsBundle\Form\DataTransformer\DeploymentNetworkTransformer;
use Veneer\OpsBundle\Form\Type\AbstractDeploymentManifestPathType;

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
                            'veneer_ops_editor_cloudconfig_network_manual',
                            [
                                'label' => 'Manual',
                                'manifest' => $options['manifest'],
                                'manifest_path' => $options['manifest_path'],
                            ],
                        ],
                        'dynamic' => [
                            'veneer_ops_editor_cloudconfig_network_dynamic',
                            [
                                'label' => 'Dynamic',
                                'manifest' => $options['manifest'],
                                'manifest_path' => $options['manifest_path'],
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
        return 'veneer_ops_editor_cloudconfig_network';
    }
}
