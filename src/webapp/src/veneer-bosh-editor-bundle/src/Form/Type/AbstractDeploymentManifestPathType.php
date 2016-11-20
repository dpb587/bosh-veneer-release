<?php

namespace Veneer\BoshEditorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractDeploymentManifestPathType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired([
            'manifest',
            'manifest_file',
        ]);
    }
}
