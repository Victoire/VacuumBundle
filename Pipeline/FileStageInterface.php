<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline;

interface FileStageInterface extends StageInterface
{
    public function __construct($kernerRootDir);
}