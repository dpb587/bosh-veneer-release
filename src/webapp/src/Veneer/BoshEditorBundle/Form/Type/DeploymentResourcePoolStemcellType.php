<?php

namespace Veneer\BoshEditorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentResourcePoolStemcellType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                [
                    'label' => 'Name',
                ]
            )
            ->add(
                'version',
                'text',
                [
                    'label' => 'Version',
                ]
            )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults([
            'label' => 'Stemcell',
        ]);
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'veneer_bosheditor_deployment_resourcepool_stemcell';
    }
}
