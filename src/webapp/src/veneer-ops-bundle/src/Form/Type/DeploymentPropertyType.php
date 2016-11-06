<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;
use Veneer\OpsBundle\Form\DataTransformer\DeploymentPropertyTransformer;

class DeploymentPropertyType extends AbstractDeploymentManifestPathType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'value',
                $options['value_type'],
                $options['value_options']
            )
            ->addModelTransformer(new DeploymentPropertyTransformer())
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'value_options' => [],
        ]);
        $resolver->setRequired([
            'value_type',
        ]);
    }

    public function getName()
    {
        return 'veneer_ops_deployment_property';
    }
}
