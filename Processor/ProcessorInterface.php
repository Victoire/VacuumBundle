<?php

namespace Victoire\DevTools\VacuumBundle\Processor;

interface ProcessorInterface
{
    /**
     * @param array $stages
     * @param $playload
     * @return mixed
     */
    public function process(array $stages, $playload);
}