<?php

namespace Veneer\BoshEditorBundle\Service\SchemaMap\FormBuilder;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface as SymfonyFormBuilderInterface;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Service\SchemaMap\FormBuilder\FormBuilderInterface;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\SchemaNodeInterface;

class VmTypeFormBuilder implements FormBuilderInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(SymfonyFormBuilderInterface $builder, SchemaNodeInterface $schema, $name, array $options)
    {
        $cloudConfig = $this->em->getRepository('VeneerBoshBundle:CloudConfigs')->findOneBy([], ['id' => 'DESC']);

        if (!$cloudConfig) {
            $builder->add($name, 'text', $options);

            return;
        }

        $cloudConfigHash = Yaml::parse($cloudConfig['properties']);

        $builder->add(
            $name,
            'choice',
            array_merge(
                $options,
                [
                    'choices' => array_map(
                        function (array $vmType) {
                            return $vmType['name'];
                        },
                        $cloudConfigHash['vm_types']
                    ),
                ]
            )
        );
    }
}