<?php

namespace Veneer\CoreBundle\Service\Workspace;

use Doctrine\ORM\EntityManager;
use Veneer\CoreBundle\Service\Workspace\Checkout\CheckoutInterface;
use Veneer\CoreBundle\Service\Workspace\Checkout\LiveCheckout;

class LiveRepository implements RepositoryInterface
{
    protected $em;
    protected $draftsRoot;

    public function __construct(EntityManager $em, $draftsRoot = null)
    {
        $this->em = $em;
        $this->draftsRoot = $draftsRoot;
    }

    public function createCheckout($ref = 'HEAD', $mode = 0)
    {
        return new LiveCheckout($this->em, $this->draftsRoot, $mode);
    }

    public function commitCheckout(Checkout\PhysicalCheckout $checkout, $message, array $options = [])
    {
        // @todo push changes
        return true;
    }

    public function listDirectory($directory = '.', $ref = 'HEAD')
    {
        return $this->createCheckout($ref)->ls($directory);
    }

    public function showFile($file, $ref = 'HEAD')
    {
        return $this->createCheckout($ref)->get($file);
    }

    public function getPrefixedPath($path)
    {
        return ltrim($this->pathPrefix . '/' . $path, '/');
    }

    public function diff($oldRef, $newRef)
    {
        throw new \BadMethodCallException('Unsupported');
    }

    public function commit($profile, array $writes, $message = null)
    {
        // TODO: Implement commit() method.
    }

    public function getDraftProfile($draft, $path)
    {
        $refRead = 'master';
        $draftStarted = false;
        $pathTainted = false;
        $draftLastDate = null;
        $draftLastAuthor = null;

        return [
            'ref_read' => $refRead,
            'ref_write' => $refRead,
            'draft_started' => $draftStarted,
            'draft_last_author' => $draftLastAuthor,
            'draft_last_date' => $draftLastDate,
            'path_tainted' => $pathTainted,
        ];
    }
}
