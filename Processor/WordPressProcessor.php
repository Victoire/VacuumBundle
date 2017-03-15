<?php

namespace Victoire\DevTools\VacuumBundle\Processor;

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