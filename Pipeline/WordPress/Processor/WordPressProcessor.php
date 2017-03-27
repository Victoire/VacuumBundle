<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Processor;

use Victoire\DevTools\VacuumBundle\Pipeline\ProcessorInterface;

class WordPressProcessor implements ProcessorInterface
{
    /**
     * @param array $stages
     * @param $playload
     * @return mixed
     */
    public function process(array $stages, $playload)
    {
        foreach ($stages as $stage) {
            $playload = call_user_func($stage, $playload);
        }

        return $playload;
    }
}