<?php

namespace Veneer\CoreBundle\Service\Workspace\Editor;

interface EditorInterface
{
    public function getTitle();
    public function getDescription();
    public function getRoute();
}
