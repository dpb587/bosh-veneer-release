<?php

namespace Veneer\BoshEditorBundle\Form\Type;

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
        return 'veneer_bosh_editor_deployment_diskpool_manifestselector';
    }
}
