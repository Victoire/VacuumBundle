<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline;

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

    /**
     * Execute the processor process method.
     *
     * @param $payload
     *
     * @return mixed
     */
    public function process($payload);
}
