<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Pipeline\WordPressPipeline;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Processor\WordPressProcessor;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\ArticleDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\VicArticleAttachmentStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\VicArticleContentStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\VicArticleGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Author\AuthorDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog\BlogDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog\VicBlogGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category\CategoryDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category\VicCategoryGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\FinalStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Pages\VicArticlesBusinessPagesStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\SEO\VicSEOGenerator;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag\TagDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag\VicTagGeneratorStages;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Template\VicArticleTemplateBuilder;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayload;
use Victoire\DevTools\VacuumBundle\Utils\Curl\CurlsTools;
use Victoire\DevTools\VacuumBundle\Utils\Media\MediaFormater;

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
     * @var string
     */
    private $kernelRootDir;

    /**
     * @var MediaFormater
     */
    private  $mediaFormater;

    /**
     * @var CurlsTools
     */
    private $curlsTools;

    /**
     * IOWordPressPipeline constructor.
     * @param $data
     */
    public function __construct(
        EntityManager $entityManager,
        $kernelRootDir,
        MediaFormater $mediaFormater,
        CurlsTools $curlsTools
    )
    {
        $this->entityManager = $entityManager;
        $this->kernelRootDir = $kernelRootDir;
        $this->mediaFormater = $mediaFormater;
        $this->curlsTools = $curlsTools;
    }

    /**
     * @param $input
     */
    public function preparePipeline($commandParameter, OutputInterface $output, QuestionHelper $questionHelper)
    {
        $raw = file_get_contents($commandParameter['dump']);
        $raw = str_replace(["wp:","dc:",":encoded"],"",$raw);
        $rawData = simplexml_load_string($raw);

        $playload = new CommandPlayload($commandParameter, $output, $questionHelper, $rawData);

        $pipeline = new WordPressPipeline([], new WordPressProcessor());

        $pipeline
            ->pipe(new BlogDataExtractorStages())
            ->pipe(new AuthorDataExtractorStages($this->entityManager))
            ->pipe(new CategoryDataExtractorStages())
            ->pipe(new TagDataExtractorStages())
            ->pipe(new VicBlogGeneratorStages($this->entityManager))
            ->pipe(new VicCategoryGeneratorStages($this->entityManager))
            ->pipe(new VicTagGeneratorStages($this->entityManager))
            ->pipe(new ArticleDataExtractorStages())
//            ->pipe(new VicArticleAttachmentStages($this->mediaFormater))
//            ->pipe(new VicArticleContentStages($this->mediaFormater))
            ->pipe(new VicArticleGeneratorStages($this->entityManager))
            ->pipe(new VicArticleTemplateBuilder($this->entityManager))
            ->pipe(new VicArticlesBusinessPagesStages($this->entityManager))
            ->pipe(new FinalStages($this->entityManager))
            ->pipe(new VicSEOGenerator($this->entityManager))
            ->pipe(new FinalStages($this->entityManager))
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