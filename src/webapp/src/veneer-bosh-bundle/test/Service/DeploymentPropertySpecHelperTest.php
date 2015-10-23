<?php

namespace Veneer\BoshBundle\Test\Service;

use Veneer\BoshBundle\Service\DeploymentPropertySpecHelper;

class DeploymentPropertySpecHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertSpecToTreeReflexive()
    {
        $spec = [
            'first.second' => [
                'description' => 'Second level',
                'default' => null,
            ],
            'firstish' => [
                'description' => 'First level',
                'example' => 'something',
            ],
        ];

        $tree = [
            'first' => [
                'property' => 'first',
                'children' => [
                    'second' => [
                        'property' => 'first.second',
                        'value' => [
                            'description' => 'Second level',
                            'default' => null,
                        ],
                    ],
                ],
            ],
            'firstish' => [
                'property' => 'firstish',
                'value' => [
                    'description' => 'First level',
                    'example' => 'something',
                ],
            ],
        ];

        $this->assertEquals($tree, DeploymentPropertySpecHelper::convertSpecToTree($spec));
        $this->assertEquals($spec, DeploymentPropertySpecHelper::convertTreeToSpec($tree));
    }
}
