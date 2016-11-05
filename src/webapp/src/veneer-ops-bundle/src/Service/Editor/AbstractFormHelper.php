<?php

namespace Veneer\OpsBundle\Service\Editor;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Veneer\BoshBundle\Service\DeploymentPropertySpecHelper;
use Veneer\BoshBundle\Model\DeploymentProperties;

abstract class AbstractFormHelper
{
    protected $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    protected function filterFormBuilder(FormBuilderInterface $formBuilder, array $path)
    {
        if (0 == count($path)) {
            return;
        }

        $found = false;

        foreach ($formBuilder->all() as $name => $formBuilderChild) {
            if ($path[0] == $name) {
                $this->filterFormBuilder($formBuilderChild, array_slice($path, 1));
                $found = true;
            } else {
                $formBuilder->remove($name);
            }
        }

        if (!$found) {
            throw new \InvalidArgumentException(sprintf('Subpath "%s" does not exist', $path[0]));
        }
    }

    protected function lookupNamedIndex(array $manifest, $concept, $name, $type)
    {
        if (!isset($manifest[$concept])) {
            return [
                'isset' => false,
                'path' => '[' . $concept . '][0]',
                'type' => $type,
                'title' => ucwords(strtr($concept, '_', ' ')),
                'data' => [
                    'name' => $name,
                ],
            ];
        }

        if ('' != $name) {
            foreach ($manifest[$concept] as $conceptIdx => $conceptData) {
                if ($name != $conceptData['name']) {
                    continue;
                }

                return [
                    'isset' => true,
                    'path' => '[' . $concept . '][' . $conceptIdx . ']',
                    'type' => $type,
                    'title' => ucwords(strtr($concept, '_', ' ')),
                    'subtitle' => $name,
                    'data' => $conceptData,
                ];
            }
        }

        return [
            'isset' => false,
            'path' => '[' . $concept . '][' . count($manifest[$concept]) . ']',
            'type' => $type,
            'title' => ucwords(strtr($concept, '_', ' ')),
            'subtitle' => 'New',
            'data' => [
                'name' => $name,
            ],
        ];
    }
}
