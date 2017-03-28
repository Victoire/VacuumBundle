<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Tag;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class TagDataExtractorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag
 */
class TagDataExtractorStages implements StageInterface
{
    /**
     * @param $playload
     * @return mixed
     */
    public function __invoke($playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        foreach ($playload->getRawData()->channel as $blog) {
            foreach ($blog->tag as $wpTag) {
                $tag = new Tag();
                $tag->setTerm($playload->getTerm($xmlDataFormater->formatInteger('term_id', $wpTag)));
                $tag->setTagName($xmlDataFormater->formatString('tag_name', $wpTag));
                $tag->setTagSlug($xmlDataFormater->formatString('tag_slug', $wpTag));

                $playload->addTag($tag);
            }
        }

        unset($xmlDataFormater);
        return $playload;
    }
}