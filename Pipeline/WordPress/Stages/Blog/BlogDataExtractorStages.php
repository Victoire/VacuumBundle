<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog;

use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class BlogDataExtractorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog
 */
class BlogDataExtractorStages implements StageInterface
{
    /**
     * @param $playload
     * @return mixed
     */
    public function __invoke(PlayloadInterface $playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        $progress = $playload->getProgressBar(count($playload->getRawData()->channel));
        $playload->getOutput()->writeln(sprintf('Blog data extraction:'));

        foreach ($playload->getRawData()->channel as $key => $blog) {
            $playload->setTitle($playload->getParameters()['blog_name']);
            $playload->setLink($xmlDataFormater->formatString('link', $blog));
            $playload->setPublicationDate($xmlDataFormater->formatDate('pubDate', $blog));
            $playload->setDescription($xmlDataFormater->formatString('description', $blog));
            $playload->setLanguage($xmlDataFormater->formatString('language', $blog));
            $playload->setBaseSiteUrl($xmlDataFormater->formatString('base_site_url', $blog));
            $playload->setBaseBlogUrl($xmlDataFormater->formatString('base_blog_url', $blog));
            $progress->advance();
        }
        $progress->finish();
        $playload->getOutput()->writeln(sprintf(' success'));

        unset($xmlDataFormater);
        return $playload;
    }
}