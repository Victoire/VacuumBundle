<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline;

interface StageInterface
{
    public function __invoke(PlayloadInterface $playload);
}