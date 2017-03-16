<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\Stages;

use Victoire\DevTools\VacuumBundle\Entity\Playload;

interface StageInterface
{
    public function __invoke(Playload $playload);
}