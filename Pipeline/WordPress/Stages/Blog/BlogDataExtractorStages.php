<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class BlogDataExtractorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog
 */
class BlogDataExtractorStages implements StageInterface
{
    /**
     * Will extract the blog from raw data
     * stop the command if more than one blog is in the dump
     *
     * @param $playload
     * @return mixed
     */
    public function __invoke(CommandPlayloadInterface $playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        $progress = $playload->getNewProgressBar(count($playload->getRawData()->channel));
        $playload->getNewStageTitleMessage("Blog data extraction:");

        if (count($playload->getRawData()->channel) > 1) {
            $playload->throwErrorAndStop("Dump has more than on blog in it.");
        } else {
            $channel = $playload->getRawData()->channel;
            $blog = new Blog();
            $blog->setTitle($playload->getParameters()['blog_name']);
            $blog->setLink($xmlDataFormater->formatString('link', $channel));
            $blog->setPublicationDate($xmlDataFormater->formatDate('pubDate', $channel));
            $blog->setDescription($xmlDataFormater->formatString('description', $channel));

            $locale = $xmlDataFormater->formatString("language", $channel);
            $locale = explode("-", $locale);
            $blog->setLocale($locale[0]);

            $blog->setBaseSiteUrl($xmlDataFormater->formatString('base_site_url', $channel));
            $blog->setBaseBlogUrl($xmlDataFormater->formatString('base_blog_url', $channel));

            $playload->setTmpBlog($blog);
        }

        $progress->finish();
        $playload->getNewSuccessMessage(" success");
        $playload->jumpLine();

        unset($xmlDataFormater);
        return $playload;
    }
}