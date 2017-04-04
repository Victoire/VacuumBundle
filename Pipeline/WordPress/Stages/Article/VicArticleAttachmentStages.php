<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Victoire\Bundle\MediaBundle\Entity\Media;
use Victoire\DevTools\VacuumBundle\Pipeline\FileStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
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
     * @param $playload
     */
    public function __invoke(PlayloadInterface $playload)
    {
        $blogFolder = $this->mediaFormater->generateBlogFolder($playload);

        $progress = $playload->getProgressBar(count($playload->getItems()));
        $playload->getOutput()->writeln(sprintf('Victoire Article Media generation:'));

        foreach ($playload->getItems() as $plArticle) {
            if (null != $plArticle->getAttachmentUrl()) {
                $articleFolder = $this->mediaFormater->generateFoler($plArticle->getTitle(), $blogFolder);
                $distantPath = $this->mediaFormater->cleanUrl($plArticle->getAttachmentUrl());
                $plArticle->setAttachment($this->mediaFormater->generateImageMedia($distantPath, $articleFolder));
                $progress->advance();
            }
        }

        $playload->getSuccess();

        return $playload;
    }
}