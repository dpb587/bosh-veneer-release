<?php

namespace Veneer\MarketplaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="marketplace_releaseversion")
 * @ORM\Entity
 */
class ReleaseVersion
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
     * @ORM\Column(name="release", type="string", length=128)
     */
    protected $release;

    public function setRelease($release)
    {
        $this->release = $release;

        return $this;
    }

    public function getRelease()
    {
        return $this->release;
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
     * @ORM\Column(name="detail_url", type="text")
     */
    protected $detailUrl;

    public function setDetailUrl($detailUrl)
    {
        $this->detailUrl = $detailUrl;

        return $this;
    }

    public function getDetailUrl()
    {
        return $this->detailUrl;
    }

    /**
     * @ORM\Column(name="tarball_url", type="text")
     */
    protected $tarballUrl;

    public function setTarballUrl($tarballUrl)
    {
        $this->tarballUrl = $tarballUrl;

        return $this;
    }

    public function getTarballUrl()
    {
        return $this->tarballUrl;
    }
}
