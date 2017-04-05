<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline;

use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;

interface StageInterface
{
    /**
     * @param CommandPlayloadInterface $playload
     * @return $playload
     */
    public function __invoke(CommandPlayloadInterface $playload);
}