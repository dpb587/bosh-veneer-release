<?php

namespace Veneer\MarketplaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="marketplace_stemcell")
 * @ORM\Entity
 */
class Stemcell
{
    /**
     * @var string
     *
     * @ORM\Column(name="marketplace", type="string", length=64)
     */
    protected $marketplace;

    public function setMarketplace($marketplace)
    {
        $this->marketplace = $marketplace;

        return $this;
    }

    public function getMarketplace()
    {
        return $this->marketplace;
    }

    /**
     * @ORM\Column(name="stemcell", type="string", length=128)
     */
    protected $stemcell;

    public function setStemcell($stemcell)
    {
        $this->stemcell = $stemcell;

        return $this;
    }

    public function getStemcell()
    {
        return $this->stemcell;
    }

    /**
     * @ORM\Column(name="version", type="string", length=128)
     */
    protected $version;

    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @ORM\Column(name="artifact_url", type="text")
     */
    protected $artifactUrl;

    public function setArtifactUrl($artifactUrl)
    {
        $this->artifactUrl = $artifactUrl;

        return $this;
    }

    public function getArtifactUrl()
    {
        return $this->artifactUrl;
    }
}
