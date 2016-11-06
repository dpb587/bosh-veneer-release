<?php

namespace Veneer\CoreBundle\Service\Workspace\Repository\GitRepository;

use Symfony\Component\Process\Process;
use Veneer\CoreBundle\Service\Workspace\Repository\Changeset;
use Veneer\CoreBundle\Service\Workspace\Repository\BlobInterface;

class GitRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected $repo;

    public function setUp()
    {
        $tmpdir = sys_get_temp_dir().'/gitrepo-'.str_replace('.', '', microtime(true));

        foreach ([
            'mkdir {cwd}',
            'git init .',
            'git config user.name "John Doe"',
            'git config user.email "jdoe@example.com"',
            'echo file1 > file1',
            'echo file2 > file2',
            'echo file3 > file3',
            'mkdir dirA',
            'echo dirA.file1 > dirA/file1',
            'cd dirA ; ln -s ../file2 file2',
            'git add -A',
            'GIT_AUTHOR_DATE="2015.10.31T01:02:03" GIT_COMMITTER_DATE="2015.10.31T01:02:03" git commit -m "first commit"',
            'git tag tag1',
            'rm file1',
            'echo file3b > file3',
            'echo file4 > file4',
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

        $this->repo = new Repository($tmpdir.'/.git');
    }

    public function tearDown()
    {
        if ($this->repo) {
            //exec('rm -fr ' . escapeshellarg(dirname($this->repo->getRoot())));
        }
    }

    public function testRepositoryRoot()
    {
        $this->assertStringStartsWith(sys_get_temp_dir().'/gitrepo-', $this->repo->getRoot());
    }

    public function testBlobFile()
    {
        $blob = $this->repo->getBlob('file3');
        $this->assertInstanceOf(Blob::class, $blob);

        $this->assertEquals(BlobInterface::STATE_CREATED, $blob->getState());
        $this->assertEquals('file3', $blob->getPath());
        $this->assertEquals(BlobInterface::TYPE_FILE, $blob->type());
        $this->assertEquals('644', $blob->mode());
        $this->assertEquals('file3b'."\n", $blob->data());
        $this->assertFalse($blob->isModified());

        $blob->mode('777');
        $blob->data('file3c');

        $this->assertTrue($blob->isModified());
        $this->assertEquals('777', $blob->mode());
        $this->assertEquals('file3c', $blob->data());
    }

    public function testBlobLink()
    {
        $blob = $this->repo->getBlob('dirA/file2');
        $this->assertInstanceOf(Blob::class, $blob);

        $this->assertEquals(BlobInterface::STATE_CREATED, $blob->getState());
        $this->assertEquals('dirA/file2', $blob->getPath());
        $this->assertEquals(BlobInterface::TYPE_LINK, $blob->type());
        $this->assertEquals('000', $blob->mode());
        $this->assertEquals('../file2', $blob->data());
        $this->assertFalse($blob->isModified());
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
                'file1' => Changeset::DELETED,
                'file3' => Changeset::MODIFIED,
                'file4' => Changeset::CREATED,
            ],
            $changes
        );

        $this->assertEquals('file3'."\n", $changeset->getOldBlob('file3')->data());
        $this->assertEquals('file3b'."\n", $changeset->getNewBlob('file3')->data());
    }

    public function testTreeTagResolution()
    {
        $this->assertEquals('da138a0972c1b9dc4911abdfbfb9a52ec658fe9c', $this->repo->getTree('tag1')->getCanonicalName());
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testTreeTagResolutionInvalid()
    {
        $this->repo->getTree('tag0')->getCanonicalName();
    }
}
