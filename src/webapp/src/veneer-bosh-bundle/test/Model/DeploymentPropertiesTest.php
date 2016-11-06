<?php

namespace Veneer\BoshBundle\Test\Model;

use Veneer\BoshBundle\Model\DeploymentProperties;

class DeploymentPropertiesTest extends \PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new DeploymentProperties([
            'first' => [
                'second' => [
                    'third' => 'Level',
                ],
            ],
            'topical' => 42,
        ]);
    }

    public function tearDown()
    {
        unset($this->sut);
    }

    public function testOffsetGet()
    {
        $this->assertEquals('Level', $this->sut['first.second.third']);
        $this->assertEquals(['third' => 'Level'], $this->sut['first.second']);
        $this->assertEquals(42, $this->sut['topical']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testOffsetGetInvalid()
    {
        $this->sut['nowhere.to.be.found'];
    }

    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->sut['first.second.third']));
        $this->assertTrue(isset($this->sut['first.second']));
        $this->assertTrue(isset($this->sut['topical']));

        $this->assertFalse(isset($this->sut['first.second.fourth']));
        $this->assertFalse(isset($this->sut['topically']));
    }

    public function testOffsetSet()
    {
        $this->sut['first.second.third'] = 'unlevel';
        $this->assertEquals('unlevel', $this->sut['first.second.third']);

        $this->sut['first.second'] = 'abandoned';
        $this->assertEquals('abandoned', $this->sut['first.second']);
        $this->assertFalse(isset($this->sut['first.second.third']));
    }

    public function testOffsetUnset()
    {
        unset($this->sut['first.second.third']);
        $this->assertFalse(isset($this->sut['first.second.third']));
    }
}
