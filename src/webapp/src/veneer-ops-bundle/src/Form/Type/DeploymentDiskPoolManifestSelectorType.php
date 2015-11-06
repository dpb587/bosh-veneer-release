<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;
use SYmfony\Component\OptionsResolver\Options;

class DeploymentDiskPoolManifestSelectorType extends AbstractDeploymentManifestPathType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'choices' => function (Options $options) {
                $opts = [];

                if (isset($options['manifest']['disk_pools'])) {
                    foreach ($options['manifest']['disk_pools'] as $resourcepool) {
                        $opts[$resourcepool['name']] = sprintf(
                            '%s (%s MB)',
                            $resourcepool['name'],
                            $resourcepool['disk_size']
                        );
                    }
                }
                
                return $opts;
            },
        ]);
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'veneer_ops_deployment_diskpool_manifestselector';
    }
}
