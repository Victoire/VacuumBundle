<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Pipeline;

use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PipelineInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\ProcessorInterface;

/**
 * Class WordPressPipeline.
 */
class WordPressPipeline implements PipelineInterface
{
    /**
     * @var array
     */
    private $stages = [];

    /**
     * @var ProcessorInterface
     */
    private $processor;

    /**
     * WordPressPipeline constructor.
     *
     * @param array                   $stages
     * @param ProcessorInterface|null $processor
     */
    public function __construct(
        array $stages = [],
        ProcessorInterface $processor = null
    ) {
        foreach ($stages as $stage) {
            if (false === is_callable($stage)) {
                throw new  \InvalidArgumentException('All stage should be callable');
            }
        }

        $this->stages = $stages;
        $this->processor = $processor;
    }

    /**
     * @param callable $operation
     *
     * @return WordPressPipeline
     */
    public function pipe(callable $operation)
    {
        $pipeline = clone $this;
        $pipeline->stages[] = $operation;

        return $pipeline;
    }

    /**
     * @param $payload
     *
     * @return mixed
     */
    public function process($payload)
    {
        return $this->processor->process($this->stages, $payload);
    }

    /**
     * @param $payload
     *
     * @return mixed
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        return $this->process($payload);
    }
}
