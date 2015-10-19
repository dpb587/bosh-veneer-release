<?php

namespace Veneer\CoreBundle\Service\Workspace\Repository\GitRepository;

use Symfony\Component\Process\Process;
use Veneer\CoreBundle\Service\Workspace\Repository\BlobInterface;
use Veneer\CoreBundle\Service\Workspace\Repository\RepositoryInterface;
use Veneer\CoreBundle\Service\Workspace\Repository\Changeset;

class Repository implements RepositoryInterface
{
    protected $root;
    protected $options;

    public function __construct($root, array $options = [])
    {
        $this->root = $root;
        $this->options = array_merge(
            [
                'default_tree' => 'master',
                'git_exec' => 'git',
            ],
            $options
        );
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function getBlob($path)
    {
        return $this->getTree($this->getDefaultTree())->getBlob($path);
    }

    public function getTree($name)
    {
        return new Tree($this, $name);
    }

    public function diff(Tree $oldTree, Tree $newTree)
    {
        $lines = explode(
            "\n",
            trim($this->exec([
                'diff',
                '--name-status',
                $oldTree->getCanonicalName(),
                $newTree->getCanonicalName(),
            ]))
        );

        $statusMap = [
            'A' => Changeset::CREATED,
            'M' => Changeset::MODIFIED,
            'D' => Changeset::DELETED,
        ];

        $changes = [];

        foreach ($lines as $line) {
            $sp = preg_split('/\s+/', $line, 2);

            $changes[$sp[1]] = $statusMap[$sp[0]];
        }

        return new Changeset($oldTree, $newTree, $changes);
    }

    public function getDefaultTree()
    {
        return $this->options['default_tree'];
    }

    public function exec(array $arguments, $stdin = null)
    {
        $p = new Process(
            implode(
                ' ',
                array_map(
                    'escapeshellarg',
                    array_merge(
                        [
                            $this->options['git_exec'],
                        ],
                        $arguments
                    )
                )
            ),
            null,
            [
                'GIT_DIR' => $this->root,
            ],
            $stdin
        );

        $p->mustRun();

        return $p->getOutput();
    }

    public function loadBlob($reference, $path)
    {
        $meta = preg_split(
            '/\s+/',
            $this->exec([
                'ls-tree',
                $reference,
                $path,
            ]),
            4
        );

        if ('12' == substr($meta[0], 0, 2)) {
            $type = BlobInterface::TYPE_LINK;
        } elseif ('10' == substr($meta[0], 0, 2)) {
            $type = BlobInterface::TYPE_FILE;
        } else {
            throw new \LogicException('Unexpected type: ' . $meta[0]);
        }

        $data = $this->exec([
            'cat-file',
            'blob',
            $meta['2'],
        ]);

        return [
            'state' => BlobInterface::STATE_CREATED,
            'type' => $type,
            'mode' => substr($meta[0], -3),
            'data' => $data,
        ];
    }
}
