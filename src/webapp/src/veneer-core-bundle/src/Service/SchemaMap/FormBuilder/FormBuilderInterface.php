<?php

namespace Veneer\CoreBundle\Service\SchemaMap\FormBuilder;

use Symfony\Component\Form\FormBuilderInterface as SymfonyFormBuilderInterface;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\SchemaNodeInterface;

interface FormBuilderInterface
{
    public function buildForm(SymfonyFormBuilderInterface $builder, SchemaNodeInterface $schema, $name, array $options);
}
