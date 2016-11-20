<?php

namespace Veneer\BoshEditorBundle\Service\Editor;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Veneer\CoreBundle\Service\SchemaMap\FormBuilder\FormBuilder;
use Veneer\CoreBundle\Service\SchemaMap\SchemaMap;
use Veneer\CoreBundle\Service\SchemaMap\DataNode\ArrayDataNode;
use Veneer\CoreBundle\Service\SchemaMap\Node;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\ArraySchemaNode;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\SchemaNodeInterface;

class SchemaMapFormHelper
{
    protected $formFactory;
    protected $schemaFormBuilder;
    protected $schemaMap;

    public function __construct(FormFactoryInterface $formFactory, FormBuilder $schemaFormBuilder, SchemaMap $schemaMap)
    {
        $this->formFactory = $formFactory;
        $this->schemaFormBuilder = $schemaFormBuilder;
        $this->schemaMap = $schemaMap;
    }

    public function getEditorNode(array $manifest, $path)
    {
        return $this->schemaMap->traverse((new ArrayDataNode(''))->setData($manifest), $path);
    }

    public function createEditor(Node $node)
    {
        $config = [
            'isset' => $node->getData()->hasData(),
            'path' => $node->getData()->getPath(),
            'data' => $node->getData()->getData(),
            'title' => isset($node->getSchema()->getSchema()->title) ? $node->getSchema()->getSchema()->title : $node->getData()->getRelativePath(),
        ];

        $formBuilder = $this->formFactory->createNamedBuilder('temporary', 'form');
        $this->schemaFormBuilder->buildForm(
            $formBuilder,
            $node->getSchema(),
            'data',
            [
                'label' => $config['title'],
            ]
        );

        $formBuilder = $formBuilder->get('data');
        $formBuilder->setData($node->getData()->getData());
        $form = $formBuilder->getForm();

        return [
            'form' => $form,
            'isset' => $config['isset'],
            'path' => $config['path'],
            'data' => $config['data'],
            'title' => $config['title'],
        ];
    }
}
