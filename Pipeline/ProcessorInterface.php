<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline;

interface ProcessorInterface
{
    /**
     * @param array $stages
     * @param $playload
     * @return mixed
     */
    public function process(array $stages, $playload);
}