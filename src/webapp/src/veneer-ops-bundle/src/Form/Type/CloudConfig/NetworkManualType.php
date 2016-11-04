<?php

namespace Veneer\OpsBundle\Form\Type\CloudConfig;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;
use Veneer\OpsBundle\Form\Type\AbstractDeploymentManifestPathType;

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
                    'type' => 'veneer_ops_editor_cloudconfig_network_manual_subnet',
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
        return 'veneer_ops_editor_cloudconfig_network_manual';
    }
}
