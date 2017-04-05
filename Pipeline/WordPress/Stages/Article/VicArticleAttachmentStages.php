<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Victoire\Bundle\MediaBundle\Entity\Media;
use Victoire\DevTools\VacuumBundle\Pipeline\FileStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayload;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;
use Victoire\DevTools\VacuumBundle\Utils\Media\MediaFormater;

/**
 * Class VicArticleMediaBuilderStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article
 */
class VicArticleAttachmentStages implements StageInterface
{
    /**
     * @var MediaFormater
     */
    private $mediaFormater;

    /**
     * VicArticleAttachmentStages constructor.
     * @param MediaFormater $mediaFormater
     */
    public function __construct(
        MediaFormater $mediaFormater
    )
    {
        $this->mediaFormater = $mediaFormater;
    }

    /**
     * Generate a Victoire Media Entity based on
     * article attachment url.
     *
     * @param CommandPlayloadInterface $playload
     * @return CommandPlayloadInterface
     */
    public function __invoke(CommandPlayloadInterface $playload)
    {
        $blogFolder = $this->mediaFormater->generateBlogFolder($playload);

        $progress = $playload->getNewProgressBar(count($playload->getTmpBlog()->getArticles()));
        $playload->getNewStageTitleMessage('Victoire Article Media generation:');

        foreach ($playload->getTmpBlog()->getArticles() as $plArticle) {
            if (null != $plArticle->getAttachmentUrl()) {
                $articleFolder = $this->mediaFormater->generateFoler($plArticle->getTitle(), $blogFolder);
                $distantPath = $this->mediaFormater->cleanUrl($plArticle->getAttachmentUrl());
                $plArticle->setAttachment($this->mediaFormater->generateImageMedia($distantPath, $articleFolder));
                $progress->advance();
            }
        }

        $playload->jumpLine();
        $playload->getNewSuccessMessage(" success");
        return $playload;
    }
}