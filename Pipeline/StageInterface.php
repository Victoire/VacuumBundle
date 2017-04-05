<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline;

use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;

interface StageInterface
{
    public function __invoke(CommandPlayloadInterface $playload);
}