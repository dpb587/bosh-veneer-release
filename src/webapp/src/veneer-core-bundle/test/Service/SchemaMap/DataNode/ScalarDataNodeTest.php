<?php

namespace Veneer\CoreBundle\Tests\Service\SchemaMap\Node;

use Veneer\CoreBundle\Service\SchemaMap\DataNode\ArrayDataNode;
use Veneer\CoreBundle\Service\SchemaMap\DataNode\ScalarDataNode;

class ScalarDataNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ScalarDataNode
     */
    protected $subject;

    public function testParent()
    {
        $parent = new ArrayDataNode('parent');

        $this->assertSame($this->subject, $this->subject->setParent($parent));
        $this->assertSame($parent, $this->subject->getParent());
    }

    public function testRelativePath()
    {
        $this->assertEquals('subject', $this->subject->getRelativePath());
    }

    public function testPath()
    {
        $this->subject->setParent(new ArrayDataNode('root'));
        $this->assertEquals('/root/subject', $this->subject->getPath());
    }

    public function testPathAsParent()
    {
        $this->assertEquals('/subject', (new ScalarDataNode('subject'))->getPath());
    }

    public function testDataDoesNotExistInitially()
    {
        $this->assertFalse($this->subject->hasData());
    }

    public function testData()
    {
        $this->assertSame($this->subject, $this->subject->setData('something'));
        $this->assertTrue($this->subject->hasData());
        $this->assertEquals('something', $this->subject->getData());
    }

    protected function setUp()
    {
        $this->subject = new ScalarDataNode('subject');
    }

    protected function tearDown()
    {
        $this->subject = null;
    }
}