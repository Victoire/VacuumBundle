<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Article;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class ArticleDataExtractorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article
 */
class ArticleDataExtractorStages implements StageInterface
{
    /**
     * @param $playload
     * @return mixed
     */
    public function __invoke($playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        foreach ($playload->getRawData()->channel as $blog) {
            foreach ($blog->item as $wpArticle) {
                $postType = $xmlDataFormater->formatString('post_type', $wpArticle);
                if ($postType == "post") {
                    $article = new Article();
                    $article->setTitle($xmlDataFormater->formatString('title', $wpArticle));
                    $article->setLink($xmlDataFormater->formatString('link', $wpArticle));
                    $article->setPubDate($xmlDataFormater->formatDate('pubDate', $wpArticle));
                    $article->setCreator($playload->getAuthor($xmlDataFormater->formatString('creator', $wpArticle)));
                    $article->setDescription($xmlDataFormater->formatString('description', $wpArticle));
                    $article->setContent($xmlDataFormater->formatString('content', $wpArticle));
                    $article->setPostId($xmlDataFormater->formatInteger('post_id', $wpArticle));
                    $article->setPostDate($xmlDataFormater->formatDate('post_date', $wpArticle));
                    $article->setPostDateGmt($xmlDataFormater->formatDate('post_date_gmt', $wpArticle));
                    $article->setStatus($xmlDataFormater->formatString('status', $wpArticle));
                    $article->setAttachmentUrl($xmlDataFormater->formatString('attachment_url', $wpArticle));
                    $playload->addItem($article);
                }
            }
        }

        unset($xmlDataFormater);
        return $playload;
    }
}