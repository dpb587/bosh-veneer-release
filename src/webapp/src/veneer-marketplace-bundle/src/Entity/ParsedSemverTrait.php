<?php

namespace Veneer\MarketplaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ParsedSemverTrait
{
    /**
     * @ORM\Column(name="semver_major", type="integer", nullable=true)
     */
    protected $semverMajor;

    public function setSemverMajor($semverMajor)
    {
        $this->semverMajor = $semverMajor;

        return $this;
    }

    public function getSemverMajor()
    {
        return $this->semverMajor;
    }

    /**
     * @ORM\Column(name="semver_minor", type="integer", nullable=true)
     */
    protected $semverMinor;

    public function setSemverMinor($semverMinor)
    {
        $this->semverMinor = $semverMinor;

        return $this;
    }

    public function getSemverMinor()
    {
        return $this->semverMinor;
    }

    /**
     * @ORM\Column(name="semver_patch", type="integer", nullable=true)
     */
    protected $semverPatch;

    public function setSemverPatch($semverPatch)
    {
        $this->semverPatch = $semverPatch;

        return $this;
    }

    public function getSemverPatch()
    {
        return $this->semverPatch;
    }

    /**
     * @ORM\Column(name="semver_extra", type="string", length=16, nullable=true)
     */
    protected $semverExtra;

    public function setSemverExtra($semverExtra)
    {
        $this->semverExtra = $semverExtra;

        return $this;
    }

    public function getSemverExtra()
    {
        return $this->semverExtra;
    }

    /**
     * @ORM\Column(name="semver_stability", type="string", length=8, nullable=true)
     */
    protected $semverStability;

    public function setSemverStability($semverStability)
    {
        $this->semverStability = $semverStability;

        return $this;
    }

    public function getSemverStability()
    {
        return $this->semverStability;
    }
}
