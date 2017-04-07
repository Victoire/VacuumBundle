<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Tag;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;
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
     * @param $payload
     * @return mixed
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $xmlDataFormater = new XmlDataFormater();

        $channel = $payload->getRawData()->channel;

        $progress = $payload->getNewProgressBar(count($channel->tag));
        $payload->getNewStageTitleMessage("Tag data extraction:");

        foreach ($channel->tag as $wpTag) {
            $tag = new Tag();
            $tag->setId($xmlDataFormater->formatString('term_id', $wpTag));
            $tag->setXmlTag("tag");
            $tag->setTagName($xmlDataFormater->formatString('tag_name', $wpTag));
            $tag->setTagSlug($xmlDataFormater->formatString('tag_slug', $wpTag));

            $payload->getTmpBlog()->addTag($tag);
            $progress->advance();
        }

        $progress->finish();
        $payload->getNewSuccessMessage(" success");
        $payload->jumpLine();

        unset($xmlDataFormater);
        return $payload;
    }
}