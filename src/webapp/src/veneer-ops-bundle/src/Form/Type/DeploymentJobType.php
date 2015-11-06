<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;
use Veneer\OpsBundle\Form\DataTransformer\ArrayToYamlTransformer;

class DeploymentJobType extends AbstractDeploymentManifestPathType
{
    protected $cpi;

    public function __construct($cpi)
    {
        $this->cpi = $cpi;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                [
                    'label' => 'Name',
                    'veneer_help_html' => '<p>A unique name used to identify and reference the resource pool</p>',
                ]
            )
            ->add(
                'instances',
                'integer',
                [
                    'label' => 'Instances',
                ]
            )
            ->add(
                'resource_pool',
                'veneer_ops_deployment_resourcepool_manifestselector',
                [
                    'label' => 'Resource Pool',
                    'manifest' => $options['manifest'],
                    'manifest_path' => $options['manifest_path'],
                ]
            )
            ->add(
                'persistent_disk_pool',
                'veneer_ops_deployment_diskpool_manifestselector',
                [
                    'label' => 'Disk Pool',
                    'required' => false,
                    'manifest' => $options['manifest'],
                    'manifest_path' => $options['manifest_path'],
                ]
            )
            ->add(
                'templates',
                'veneer_ops_deployment_job_templates',
                [
                    'label' => 'Templates',
                    'manifest' => $options['manifest'],
                    'manifest_path' => $options['manifest_path'],
                ]
            )
            ->add(
                'networks',
                'collection',
                [
                    'label' => 'Networks',
                    'type' => 'veneer_ops_deployment_job_network',
                    'options' => [
                        'manifest' => $options['manifest'],
                        'manifest_path' => $options['manifest_path'],
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
        ;
    }

    public function getName()
    {
        return 'veneer_ops_deployment_job';
    }
}
