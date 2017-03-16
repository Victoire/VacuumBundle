<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress;

use Victoire\DevTools\VacuumBundle\Entity\DataContainer\WordPressDataContainer;
use Victoire\DevTools\VacuumBundle\Entity\Playload;
use Victoire\DevTools\VacuumBundle\Pipeline\Pipeline\WordPressPipeline;
use Victoire\DevTools\VacuumBundle\Pipeline\Processor\WordPressProcessor;
use Victoire\DevTools\VacuumBundle\Pipeline\Stages\ArticleGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\Stages\ArticleHydratorStages;

class IOWordPressPipeline
{
    /**
     * @var mixed
     */
    private $input;

    /**
     * @var mixed
     */
    private $output;

    /**
     * IOWordPressPipeline constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->input = $data;
    }

    public function __invoke()
    {
        $playload = new Playload();

        foreach ($this->input->channel as $children) {
            foreach ($children->item as $key => $item) {
                $dataContainer = new WordPressDataContainer();
                $dataContainer->setRawData($item);
                $playload->addResult($dataContainer);
            }
        }

        $pipeline = new WordPressPipeline([], new WordPressProcessor());
        $pipeline
            ->pipe(new ArticleGeneratorStages())
            ->pipe(new ArticleHydratorStages())
            ->process($playload)
        ;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }
}