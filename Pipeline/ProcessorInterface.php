<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline;

interface ProcessorInterface
{
    /**
     * @param array $stages
     * @param $payload
     *
     * @return mixed
     */
    public function process(array $stages, $payload);
}
