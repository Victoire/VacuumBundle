<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Tag;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class TagDataExtractorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag
 */
class TagDataExtractorStages implements StageInterface
{
    /**
     * Extract and add tag from raw Data to tmpBlog
     *
     * @param $playload
     * @return mixed
     */
    public function __invoke(CommandPlayloadInterface $playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        $channel = $playload->getRawData()->channel;

        $progress = $playload->getNewProgressBar(count($channel->tag));
        $playload->getNewStageTitleMessage("Tag data extraction:");

        foreach ($channel->tag as $wpTag) {
            $tag = new Tag();
            $tag->setTagName($xmlDataFormater->formatString('tag_name', $wpTag));
            $tag->setTagSlug($xmlDataFormater->formatString('tag_slug', $wpTag));

            $playload->getTmpBlog()->addTag($tag);
            $progress->advance();
        }

        $progress->finish();
        $playload->getNewSuccessMessage(" success");
        $playload->jumpLine();

        unset($xmlDataFormater);
        return $playload;
    }
}