<?php

namespace Veneer\CoreBundle\Service\SchemaMap\FormBuilder;

use JsonSchema\SchemaStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface as SymfonyFormBuilderInterface;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\ArraySchemaNode;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\SchemaNodeInterface;

class FormBuilder implements FormBuilderInterface
{
    protected $jsonSchema;
    protected $container;
    protected $customizedSchemas;

    public function __construct(SchemaStorage $jsonSchema, ContainerInterface $container, array $customizedSchemas = [])
    {
        $this->jsonSchema = $jsonSchema;
        $this->container = $container;
        $this->customizedSchemas = $customizedSchemas;
    }

    // @todo make this an actual FormType
    public function buildForm(SymfonyFormBuilderInterface $builder, SchemaNodeInterface $schema, $name, array $formOptions)
    {
        $rawSchema = $this->getResolvedSchema($schema)->getSchema();

        if (isset($rawSchema->description)) {
            $formOptions['veneer_help_html'] = $rawSchema->description;
        }

        if (isset($rawSchema->title)) {
            $formOptions['label'] = $rawSchema->title;
        }

        $schema = new ArraySchemaNode($rawSchema);

        if ($this->isCustomizedSchema($schema)) {
            $this->buildCustomizedForm($builder, $schema, $name, $formOptions);
        } else {
            $this->buildBuiltinForm($builder, $schema, $name, $formOptions);
        }

        return $builder->get($name);
    }

    protected function isCustomizedSchema(SchemaNodeInterface $schema)
    {
        return isset($this->customizedSchemas[$schema->getSchemaId()]);
    }

    protected function buildCustomizedForm(SymfonyFormBuilderInterface $builder, SchemaNodeInterface $schema, $name, array $formOptions)
    {
        $this->container->get($this->customizedSchemas[$schema->getSchemaId()])->buildForm($builder, $schema, $name, $formOptions);
    }

    protected function buildBuiltinForm(SymfonyFormBuilderInterface $builder, SchemaNodeInterface $schema, $name, array $formOptions)
    {
        $rawSchema = $schema->getSchema();

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
                $oneOfSchema = $this->getResolvedSchema(new ArraySchemaNode($oneOf));

                $formOptions['forms'][$oneOfIdx] = $this->buildForm(
                    $builder->create('stub', 'form'),
                    $oneOfSchema,
                    'via_' . $oneOfIdx,
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

            if (isset($rawSchema->properties)) {
                foreach ($rawSchema->properties as $propertyName => $propertyRelativeSchema) {
                    $formOptions = [];

                    if (empty($rawSchema->required)) {
                        $formOptions['required'] = false;
                    } elseif (in_array($propertyName, $rawSchema->required)) {
                        $formOptions['required'] = true;
                    } else {
                        $formOptions['required'] = false;
                    }

                    $this->buildForm(
                        $subBuilder,
                        new ArraySchemaNode($this->jsonSchema->getSchema($this->getSchemaPath($rawSchema->id, '/properties/' . $propertyName))),
                        $propertyName,
                        $formOptions
                    );
                }
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
    }

    protected function getSchema($uri, $baseUri = null)
    {
        return new ArraySchemaNode(
            $this->jsonSchema->getSchema(
                $this->jsonSchema->getUriResolver()->resolve($uri, $baseUri)
            )
        );
    }

    protected function getResolvedSchema(SchemaNodeInterface $schema)
    {
        $rawSchema = $schema->getSchema();

        if (!isset($rawSchema->{'$ref'})) {
            return $schema;
        }

        return $this->getSchema($rawSchema->{'$ref'}, isset($rawSchema->id) ? $rawSchema->id : null);
    }

    protected function getSchemaPath($base, $suffix)
    {
        return $base . (strpos($base, '#') ? '' : '#') . $suffix;
    }
}