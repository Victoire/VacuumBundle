<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress;

use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Pipeline\WordPressPipeline;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Processor\WordPressProcessor;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\ArticleDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\VicArticleGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Author\AuthorDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog\BlogDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog\VicBlogGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category\CategoryDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category\CategoryGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag\TagDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag\TagGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Term\TermDataExtractorStages;

/**
 * Class IOWordPressPipeline
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress
 */
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

    public function process()
    {
        $raw = file_get_contents($this->input);
        $raw = str_replace(["wp:","dc:",":encoded"],"",$raw);
        $rawData = simplexml_load_string($raw);

        $playload = new WordPressPlayload();
        $playload->setRawData($rawData);

        $exctractionPipeline = new WordPressPipeline([], new WordPressProcessor());
        $generatorPipeline =  new WordPressPipeline([], new WordPressProcessor());

        $exctractionPipeline
            ->pipe(new BlogDataExtractorStages())
            ->pipe(new AuthorDataExtractorStages())
            ->pipe(new TermDataExtractorStages())
            ->pipe(new CategoryDataExtractorStages())
            ->pipe(new TagDataExtractorStages())
            ->pipe(new ArticleDataExtractorStages())
            ->pipe($generatorPipeline
                ->pipe(new VicBlogGeneratorStages())
                ->pipe(new CategoryGeneratorStages())
                ->pipe(new TagGeneratorStages())
                ->pipe(new VicArticleGeneratorStages()
                )
            )
        ->process($playload);
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }
}