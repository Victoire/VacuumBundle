<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Processor;

use Victoire\DevTools\VacuumBundle\Pipeline\ProcessorInterface;

class WordPressProcessor implements ProcessorInterface
{
    /**
     * @param array $stages
     * @param $payload
     * @return mixed
     */
    public function process(array $stages, $payload)
    {
        foreach ($stages as $stage) {
            $payload = call_user_func($stage, $payload);
        }

        return $payload;
    }
}