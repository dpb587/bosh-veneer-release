<?php

namespace Veneer\CoreBundle\Service\Workspace\App;

interface AppInterface
{
    public function getAppTitle();
    public function getAppDescription();
    public function getAppRoute();
}
