<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline;

use Victoire\DevTools\VacuumBundle\Entity\Playload;

interface StageInterface
{
    public function __invoke(Playload $playload);
}