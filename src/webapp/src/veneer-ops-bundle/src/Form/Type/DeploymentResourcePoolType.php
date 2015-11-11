<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;
use Veneer\OpsBundle\Form\DataTransformer\ArrayToYamlTransformer;

class DeploymentResourcePoolType extends AbstractDeploymentManifestPathType
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
                'network',
                'veneer_ops_deployment_network_manifestselector',
                [
                    'label' => 'Network',
                    'veneer_help_html' => '<p>References a valid network name defined in the Networks block. Newly created resource pool VMs use the described configuration.</p>',
                    'manifest' => $options['manifest'],
                    'manifest_path' => $options['manifest_path'],
                ]
            )
            ->add(
                'size',
                'integer',
                [
                    'label' => 'Pool Size',
                    'veneer_help_html' => '<p>The number of VMs in the resource pool. If you omit this value, BOSH calculates the resource pool size based on the total number of job instances that belong to this resource pool. If you specify this value, BOSH requires that the size be at least as large as the total number of job instances using it.</p>',
                    'required' => false,
                ]
            )
            ->add(
                'stemcell',
                'veneer_ops_deployment_resourcepool_stemcell',
                [
                    'label' => 'Stemcell',
                    'veneer_help_html' => '<p>The stemcell used to create resource pool VMs.</p>',
                    'manifest' => $options['manifest'],
                    'manifest_path' => $options['manifest_path'],
                ]
            )
            ->add(
                'cloud_properties',
                $this->cpi->getDeploymentResourcePoolFormType(),
                [
                    'label' => 'Cloud Properties',
                    'veneer_help_html' => '<p>IaaS-specific properties needed to create VMs.</p>',
                ]
            )
            ->add(
                'env',
                'veneer_core_yaml',
                [
                    'label' => 'VM Environment',
                    'veneer_help_html' => '<p>Describes the VM environment and provides a specific VM environment to the CPI create_stemcell call. Environment data is available to BOSH Agents as VM settings.</p>',
                    'required' => false,
                ]
            )
        ;
    }

    public function getName()
    {
        return 'veneer_ops_deployment_resourcepool';
    }
}
