<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentDiskPoolType extends AbstractDeploymentManifestPathType
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
                    'label' => 'Disk Pool Name',
                    'veneer_help_html' => '<p>A unique name used to identify and reference the disk pool.</p>',
                ]
            )
            ->add(
                'disk_size',
                'integer',
                [
                    'label' => 'Disk Size',
                    'veneer_help_html' => '<p>Size of the disk in megabytes.</p>',
                ]
            )
            ->add(
                'cloud_properties',
                $this->cpi->getDeploymentDiskPoolFormType(),
                [
                    'label' => 'Cloud Properties',
                    'veneer_help_html' => '<p>IaaS-specific properties needed to create disk.</p>',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_ops_deployment_diskpool';
    }
}
