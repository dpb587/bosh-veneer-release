<?php

namespace Veneer\CoreBundle\Service\Workspace;

interface EditorInterface
{
    public function getTitle();
    public function getDescription();
    public function getRoute();
}
