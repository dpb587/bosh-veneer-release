<?php

namespace Veneer\OpsBundle\Form\Type;

use SYmfony\Component\OptionsResolver\OptionsResolverInterface;
use SYmfony\Component\OptionsResolver\Options;

class DeploymentNetworkManifestSelectorType extends AbstractDeploymentManifestPathType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'choices' => function (Options $options) {
                $opts = [];

                if (isset($options['manifest']['networks'])) {
                    foreach ($options['manifest']['networks'] as $network) {
                        $opts[$network['name']] = sprintf('%s (%s)', $network['name'], $network['type']);
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
        return 'veneer_ops_deployment_network_manifestselector';
    }
}
