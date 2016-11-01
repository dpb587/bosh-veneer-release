<?php

namespace Veneer\HubBundle\Service;

use Veneer\HubBundle\Entity\ParsedSemverTrait;

class NaiveSemverParser
{
    static public function parse(/* ParsedSemverTrait */ $semver)
    {
        if (!preg_match('/^(?P<major>\d+)(\.(?P<minor>\d+)(\.(\d+))?)?(?P<extra>.*)$/', $semver->getVersion(), $match)) {
            $semver->setSemverMajor(null);
            $semver->setSemverMinor(null);
            $semver->setSemverPatch(null);
            $semver->setSemverExtra(null);
            $semver->setSemverStability(null);

            return;
        }

        $semver->setSemverMajor($match['major']);
        $semver->setSemverMinor(isset($match['minor']) ? $match['minor'] : 0);
        $semver->setSemverPatch(isset($match['patch']) ? $match['patch'] : 0);
        $semver->setSemverExtra(isset($match['extra']) ? $match['extra'] : 0);

        if (preg_match('/^-(alpha|beta|rc)/', $semver->getSemverExtra())) {
            $semver->setSemverStability('stable');
        } else {
            $semver->setSemverStability('dev');
        }
    }
}
