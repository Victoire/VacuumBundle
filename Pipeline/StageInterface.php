<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline;

use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;

interface StageInterface
{
    /**
     * @param CommandPayloadInterface $payload
     *
     * @return $payload
     */
    public function __invoke(CommandPayloadInterface $payload);
}
