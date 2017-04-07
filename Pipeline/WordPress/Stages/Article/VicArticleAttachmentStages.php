<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Victoire\Bundle\BlogBundle\Entity\Article;
use Victoire\Bundle\MediaBundle\Entity\Media;
use Victoire\DevTools\VacuumBundle\Pipeline\FileStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;
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
     * @param CommandPayloadInterface $payload
     * @return CommandPayloadInterface
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $blogFolder = $this->mediaFormater->generateBlogFolder($payload);

        $progress = $payload->getNewProgressBar(count($payload->getTmpBlog()->getArticles()));
        $payload->getNewStageTitleMessage('Victoire Article Media generation:');

        foreach ($payload->getTmpBlog()->getArticles() as $plArticle) {
            $history = $payload->getXMLHistoryManager()->searchHistory($plArticle, Article::class);

            if (null == $history) {
                if (null != $plArticle->getAttachmentUrl()) {
                    $articleFolder = $this->mediaFormater->generateFoler($plArticle->getTitle(), $blogFolder);
                    $distantPath = $this->mediaFormater->cleanUrl($plArticle->getAttachmentUrl());
                    $plArticle->setAttachment($this->mediaFormater->generateImageMedia($distantPath, $articleFolder));
                    $progress->advance();
                }
            }
        }

        $payload->jumpLine();
        $payload->getNewSuccessMessage(" success");
        return $payload;
    }
}