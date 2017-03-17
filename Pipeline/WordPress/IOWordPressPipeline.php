<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress;

use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Pipeline\WordPressPipeline;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Processor\WordPressProcessor;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\ArticleDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Author\AuthorDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog\BlogDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category\CategoryDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag\TagDataExtractorStages;
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
        $playload = new WordPressPlayload();
        $playload->setRawData($this->input);

        $blogPipeline = new WordPressPipeline([], new WordPressProcessor());
        $authorPipeline = new WordPressPipeline([], new WordPressProcessor());
        $termPipeline = new WordPressPipeline([], new WordPressProcessor());
        $categoryPipeline = new WordPressPipeline([], new WordPressProcessor());
        $tagPipeline = new WordPressPipeline([], new WordPressProcessor());
        $articlePipeline = new WordPressPipeline([], new WordPressProcessor());

        $blogPipeline
            ->pipe(new BlogDataExtractorStages())
            ->pipe($authorPipeline
                ->pipe(new AuthorDataExtractorStages())
            )
            ->pipe($termPipeline
                ->pipe(new TermDataExtractorStages())
            )
            ->pipe($categoryPipeline
                ->pipe(new CategoryDataExtractorStages())
            )
            ->pipe($tagPipeline
                ->pipe(new TagDataExtractorStages())
            )
            ->pipe($articlePipeline
                ->pipe(new ArticleDataExtractorStages())
            )
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