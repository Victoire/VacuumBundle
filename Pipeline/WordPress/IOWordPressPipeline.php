<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress;

use Doctrine\ORM\EntityManager;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Pipeline\WordPressPipeline;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Processor\WordPressProcessor;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\ArticleDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\VicArticleGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Author\AuthorDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog\BlogDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog\VicBlogGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category\CategoryDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category\VicCategoryGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Pages\VicArticlesBusinessPagesStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag\TagDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag\VicTagGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Template\VicArticleTemplateBuilder;
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
    private $output;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * IOWordPressPipeline constructor.
     * @param $data
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $input
     */
    public function process($input)
    {
        $raw = file_get_contents($input);
        $raw = str_replace(["wp:","dc:",":encoded"],"",$raw);
        $rawData = simplexml_load_string($raw);

        $playload = new WordPressPlayload();
        $playload->setRawData($rawData);

        $exctractionPipeline = new WordPressPipeline([], new WordPressProcessor());
        $generatorPipeline =  new WordPressPipeline([], new WordPressProcessor());
        $vicArchitecturePipeline =  new WordPressPipeline([], new WordPressProcessor());

        $exctractionPipeline
            ->pipe(new BlogDataExtractorStages())
            ->pipe(new AuthorDataExtractorStages())
            ->pipe(new TermDataExtractorStages())
            ->pipe(new CategoryDataExtractorStages())
            ->pipe(new TagDataExtractorStages())
            ->pipe(new ArticleDataExtractorStages())
            ->pipe($generatorPipeline
                ->pipe(new VicBlogGeneratorStages($this->entityManager))
                ->pipe(new VicCategoryGeneratorStages($this->entityManager))
                ->pipe(new VicTagGeneratorStages($this->entityManager))
                ->pipe(new VicArticleGeneratorStages($this->entityManager))
                    ->pipe($vicArchitecturePipeline
                        ->pipe(new VicArticleTemplateBuilder())
                        ->pipe(new VicArticlesBusinessPagesStages($this->entityManager))
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