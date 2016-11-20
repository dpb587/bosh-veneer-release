<?php

namespace Veneer\BoshEditorBundle\Service\Editor;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Veneer\CoreBundle\Service\SchemaMap\SchemaMap;
use Veneer\CoreBundle\Service\SchemaMap\DataNode\ArrayDataNode;
use Veneer\CoreBundle\Service\SchemaMap\Node;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\ArraySchemaNode;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\SchemaNodeInterface;

class SchemaMapFormHelper
{
    protected $formFactory;

    public function __construct(FormFactoryInterface $formFactory, SchemaMap $schemaMap)
    {
        $this->formFactory = $formFactory;
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
        $this->createSchemaForm(
            $node->getSchema(),
            'data',
            $formBuilder,
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

    protected function createSchemaForm(SchemaNodeInterface $schema, $name, FormBuilderInterface $builder, array $formOptions)
    {
        $rawSchema = $this->schemaMap->getResolvedSchema($schema)->getSchema();

        if (isset($rawSchema->description)) {
            $formOptions['veneer_help_html'] = $rawSchema->description;
        }

        if (isset($rawSchema->title)) {
            $formOptions['label'] = $rawSchema->title;
        }

        if (isset($rawSchema->enum)) {
            $builder->add(
                $name,
                'choice',
                array_merge(
                    $formOptions,
                    [
                        'choices' => $rawSchema->enum,
                    ]
                )
            );
        } elseif (isset($rawSchema->items)) {
            $builder->add($name, 'form');
        } elseif (isset($rawSchema->oneOf)) {
            $formOptions['forms'] = [];

            foreach ($rawSchema->oneOf as $oneOfIdx => $oneOf) {
                $oneOfSchema = $this->schemaMap->getResolvedSchema(new ArraySchemaNode($oneOf));

                $formOptions['forms'][$oneOfIdx] = $this->createSchemaForm(
                    $oneOfSchema,
                    'via_' . $oneOfIdx,
                    $builder->create('stub', 'form'),
                    [
                        'label' => isset($oneOfSchema->getSchema()->title) ? $oneOfSchema->getSchema()->title : null,
                    ]
                );
            }

            $builder->add(
                $name,
                'veneer_core_form_picker',
                $formOptions
            );
        } elseif ((!isset($rawSchema->type)) || ('string' == $rawSchema->type)) {
            $builder->add($name, 'text', $formOptions);
        } elseif ('object' == $rawSchema->type) {
            $builder->add($name, 'form', $formOptions);

            $subBuilder = $builder->get($name);

            foreach ($rawSchema->properties as $propertyName => $propertyRelativeSchema) {
                $formOptions = [];

                if (empty($rawSchema->required)) {
                    $formOptions['required'] = false;
                } elseif (in_array($propertyName, $rawSchema->required)) {
                    $formOptions['required'] = true;
                } else {
                    $formOptions['required'] = false;
                }

                $this->createSchemaForm(
                    $this->schemaMap->getSchema($this->schemaMap->getSchemaPath($rawSchema->id, '/properties/' . $propertyName)),
                    $propertyName,
                    $subBuilder,
                    $formOptions
                );
            }
        } elseif ('integer' == $rawSchema->type) {
            $builder->add($name, 'number', $formOptions);
        } elseif ('number' == $rawSchema->type) {
            $builder->add($name, 'number', $formOptions);
        } elseif ('boolean' == $rawSchema->type) {
            $formOptions['choices'] = [
                true => 'Enabled',
                false => 'Disabled',
            ];

            $builder->add($name, 'choice', $formOptions);
        } else {
            throw new \LogicException(sprintf('Unsupported field type: %s', $rawSchema->type));
        }

        return $builder->get($name);
    }
}
