<?php

namespace Veneer\Component\Workspace\GitRepository;

use Veneer\Component\Workspace\BlobInterface;

class Blob implements BlobInterface
{
    protected $state = self::STATE_UNKNOWN;
    protected $tree;
    protected $path;

    protected $checksum;
    protected $type = self::TYPE_FILE;
    protected $data;
    protected $mode = 0600;

    public function __construct(Tree $tree, $path)
    {
        $this->tree = $tree;
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getTree()
    {
        return $this->tree;
    }

    public function type($type = null)
    {
        $this->load();

        if (null !== $type) {
            $this->state = self::STATE_CREATED;
            $this->type = $type;
        }

        return $this->type;
    }

    public function mode($mode = null)
    {
        $this->load();

        if (null !== $mode) {
            $this->state = self::STATE_CREATED;
            $this->mode = $mode;
        }

        return $this->mode;
    }

    public function data($data = null)
    {
        $this->load();

        if (null !== $data) {
            $this->state = self::STATE_CREATED;
            $this->data = $data;
        }

        return $this->data;
    }

    public function delete()
    {
        $this->load();

        $this->state = self::STATE_DELETED;

        return $this;
    }

    public function isModified()
    {
        return (self::STATE_UNKNOWN !== $this->state) && ($this->checksum != $this->calculateChecksum());
    }

    protected function load()
    {
        if (self::STATE_UNKNOWN === $this->state) {
            $blob = $this->getTree()->getWorkspace()->loadBlob($this->getTree()->getCanonicalName(), $this->getPath());

            $this->state = $blob['state'];
            $this->type = $blob['type'];
            $this->mode = $blob['mode'];
            $this->data = $blob['data'];

            $this->checksum = $this->calculateChecksum();
        }
    }

    protected function calculateChecksum()
    {
        return sha1(serialize([
            'state' => $this->state,
            'type' => $this->type,
            'mode' => $this->mode,
            'data' => $this->data,
        ]));
    }
}
