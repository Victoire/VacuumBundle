<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class BlogDataExtractorStages.
 */
class BlogDataExtractorStages implements StageInterface
{
    /**
     * Will extract the blog from raw data
     * stop the command if more than one blog is in the dump.
     *
     * @param $payload
     *
     * @return mixed
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $xmlDataFormater = new XmlDataFormater();

        $progress = $payload->getNewProgressBar(count($payload->getRawData()->channel));
        $payload->getNewStageTitleMessage('Blog data extraction:');

        if (count($payload->getRawData()->channel) > 1) {
            $payload->throwErrorAndStop('Dump has more than on blog in it.');
        } else {
            $channel = $payload->getRawData()->channel;

            $blog = new Blog();
            $blog->setId(1);
            $blog->setXmlTag('channel');
            $blog->setTitle($payload->getParameters()['blog_name']);
            $blog->setLink($xmlDataFormater->formatString('link', $channel));
            $blog->setPublicationDate($xmlDataFormater->formatDate('pubDate', $channel));
            $blog->setDescription($xmlDataFormater->formatString('description', $channel));

            $locale = $xmlDataFormater->formatString('language', $channel);
            $locale = explode('-', $locale);
            $blog->setLocale($locale[0]);

            $blog->setBaseSiteUrl($xmlDataFormater->formatString('base_site_url', $channel));
            $blog->setBaseBlogUrl($xmlDataFormater->formatString('base_blog_url', $channel));

            $payload->setTmpBlog($blog);
        }

        $progress->finish();
        $payload->getNewSuccessMessage(' success');
        $payload->jumpLine();

        unset($xmlDataFormater);

        return $payload;
    }
}
