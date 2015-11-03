<?php

namespace Veneer\MarketplaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="marketplace_stemcell_version")
 * @ORM\Entity
 */
class StemcellVersion
{
    use ParsedSemverTrait;

    /**
     * @ORM\Id
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
     * @ORM\Id
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
     * @ORM\Id
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
     * @ORM\Column(name="source_type", type="string", length=16)
     */
    protected $sourceType;

    public function setSourceType($sourceType)
    {
        $this->sourceType = $sourceType;

        return $this;
    }

    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * @ORM\Column(name="detail_url", type="text", nullable=true)
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

    /**
     * @ORM\Column(name="tarball_size", type="integer", nullable=true)
     */
    protected $tarballSize;

    public function setTarballSize($tarballSize)
    {
        $this->tarballSize = $tarballSize;

        return $this;
    }

    public function getTarballSize()
    {
        return $this->tarballSize;
    }

    /**
     * @ORM\Column(name="tarball_checksum", type="string", length=128)
     */
    protected $tarballChecksum;

    public function setTarballChecksum($tarballChecksum)
    {
        $this->tarballChecksum = $tarballChecksum;

        return $this;
    }

    public function getTarballChecksum()
    {
        return $this->tarballChecksum;
    }

    /**
     * @ORM\Column(name="stat_first_seen_at", type="datetime")
     */
    protected $statFirstSeenAt;

    public function setStatFirstSeenAt(\DateTime $statFirstSeenAt)
    {
        $this->statFirstSeenAt = $statFirstSeenAt;

        return $this;
    }

    public function getStatFirstSeenAt()
    {
        return $this->statFirstSeenAt;
    }

    /**
     * @ORM\Column(name="stat_last_seen_at", type="datetime")
     */
    protected $statLastSeenAt;

    public function setStatLastSeenAt(\DateTime $statLastSeenAt)
    {
        $this->statLastSeenAt = $statLastSeenAt;

        return $this;
    }

    public function getStatLastSeenAt()
    {
        return $this->statLastSeenAt;
    }
}
