<?php

namespace Veneer\CoreBundle\Service\Workspace\Repository;

interface BlobInterface
{
    const STATE_UNKNOWN = 'unknown';
    const STATE_CREATED = 'created';
    const STATE_DELETED = 'deleted';

    const TYPE_FILE = 'file';
    const TYPE_LINK = 'link';

    public function getPath();
    public function getTree();
    public function isModified();
    public function delete();

    public function type($type = null);
    public function mode($mode = null);
    public function data($data = null);
}
