<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;
use SYmfony\Component\OptionsResolver\Options;

class DeploymentResourcePoolManifestSelectorType extends AbstractDeploymentManifestPathType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'choices' => function (Options $options) {
                $opts = [];

                if (isset($options['manifest']['resource_pools'])) {
                    foreach ($options['manifest']['resource_pools'] as $resourcepool) {
                        $opts[$resourcepool['name']] = sprintf(
                            '%s (%s/%s, %s)',
                            $resourcepool['name'],
                            $resourcepool['stemcell']['name'],
                            $resourcepool['stemcell']['version'],
                            $resourcepool['network']
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
        return 'veneer_ops_deployment_resourcepool_manifestselector';
    }
}
