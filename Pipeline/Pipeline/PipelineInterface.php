<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\Pipeline;

use Victoire\DevTools\VacuumBundle\Pipeline\Stages\StageInterface;

interface PipelineInterface extends StageInterface
{
    /**
     * Create a new pipeline with an appended stage.
     *
     * @param callable $operation
     *
     * @return static
     */
    public function pipe(callable $operation);
}