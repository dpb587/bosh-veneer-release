<?php

namespace Veneer\CoreBundle\Service\Workspace\Repository\GitRepository;

use Symfony\Component\Process\Process;
use Veneer\CoreBundle\Service\Workspace\Repository\Changeset;
use Veneer\CoreBundle\Service\Workspace\Repository\BlobInterface;
use Veneer\CoreBundle\Service\Workspace\Repository\GitRepository\Blob;
use Veneer\CoreBundle\Service\Workspace\Repository\GitRepository\Tree;
use Veneer\CoreBundle\Service\Workspace\Repository\GitRepository\Repository;

class ComprehensiveTest extends \PHPUnit_Framework_TestCase
{
    protected $repo;

    public function setUp()
    {
        $tmpdir = sys_get_temp_dir() . '/gitrepo-' . str_replace('.', '', microtime(true));

        foreach ([
            'mkdir {cwd}',
            'git init .',
            'git config user.name "John Doe"',
            'git config user.email "jdoe@example.com"',
            'echo "test1" > test1',
            'echo "test2" > test2',
            'echo "test3" > test3',
            'git add -A',
            'GIT_AUTHOR_DATE="2015.10.31T01:02:03" GIT_COMMITTER_DATE="2015.10.31T01:02:03" git commit -m "first commit"',
            'git tag tag1',
            'rm test1',
            'echo "test3b" > test3',
            'echo "test4" > test4',
            'git add -A',
            'GIT_AUTHOR_DATE="2015.10.31T01:02:03" GIT_COMMITTER_DATE="2015.10.31T01:02:03" git commit -m "second commit"',
        ] as $exec) {
            $p = new Process(
                str_replace(
                    '{cwd}',
                    escapeshellarg($tmpdir),
                    $exec
                ),
                $tmpdir
            );

            $p->mustRun();
        }

        $this->repo = new Repository($tmpdir . '/.git');
    }

    public function tearDown()
    {
        if ($this->repo) {
            exec('rm -fr ' . escapeshellarg(dirname($this->repo->getRoot())));
        }
    }

    public function testRepositoryRoot()
    {
        $this->assertStringStartsWith(sys_get_temp_dir() . '/gitrepo-', $this->repo->getRoot());
    }

    public function testBlob()
    {
        $blob = $this->repo->getBlob('test3');
        $this->assertInstanceOf(Blob::class, $blob);

        $this->assertEquals(BlobInterface::STATE_CREATED, $blob->getState());
        $this->assertEquals('test3', $blob->getPath());
        $this->assertEquals(BlobInterface::TYPE_FILE, $blob->type());
        $this->assertEquals('644', $blob->mode());
        $this->assertEquals("test3b\n", $blob->data());
        $this->assertFalse($blob->isModified());

        $blob->mode('777');
        $blob->data('test3c');

        $this->assertTrue($blob->isModified());
        $this->assertEquals('777', $blob->mode());
        $this->assertEquals('test3c', $blob->data());
    }

    public function testChangeset()
    {
        $oldTree = $this->repo->getTree('tag1');
        $newTree = $this->repo->getTree('HEAD');
        $changeset = $this->repo->diff($oldTree, $newTree);

        $this->assertInstanceOf(Changeset::class, $changeset);
        $this->assertSame($oldTree, $changeset->getOldTree());
        $this->assertSame($newTree, $changeset->getNewTree());

        $changes = iterator_to_array($changeset);

        $this->assertCount(3, $changes);
        $this->assertEquals(
            [
                'test1' => Changeset::DELETED,
                'test3' => Changeset::MODIFIED,
                'test4' => Changeset::CREATED,
            ],
            $changes
        );

        $this->assertEquals("test3\n", $changeset->getOldBlob('test3')->data());
        $this->assertEquals("test3b\n", $changeset->getNewBlob('test3')->data());
    }

    public function testTreeTagResolution()
    {
        $this->assertEquals('9b534bfe807f1d170cc1fbebe3af3dc0a389ecd1', $this->repo->getTree('tag1')->getCanonicalName());
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testTreeTagResolutionInvalid()
    {
        $this->repo->getTree('tag0')->getCanonicalName();
    }
}
