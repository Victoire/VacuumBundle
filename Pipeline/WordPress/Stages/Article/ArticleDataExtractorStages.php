<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Gedmo\Tree\Mapping\Driver\Xml;
use Victoire\Bundle\SeoBundle\Entity\PageSeo;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\Article;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayload;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;
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
    public function __invoke(CommandPlayloadInterface $playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        $typePost = [];
        $typeAttachement = [];

        $channel = $playload->getRawData()->channel;

        foreach ($channel->item as $wpArticle) {
            $postType = $xmlDataFormater->formatString('post_type', $wpArticle);
            if ($postType == "post") {
                array_push($typePost, $wpArticle);
            } elseif ($postType == "attachment") {
                array_push($typeAttachement, $wpArticle);
            }
        }

        $progress = $playload->getNewProgressBar(count($typePost));
        $playload->getNewStageTitleMessage("Article Data extraction:");

        foreach ($typePost as $wpArticle) {

            $article = new Article();

            $article = self::hydrateArticle($article, $wpArticle, $playload, $xmlDataFormater);
            $article = self::manageArticleAttachment($article, $typeAttachement, $xmlDataFormater);
            $article = self::setCategoryAndTag($article, $wpArticle, $xmlDataFormater, $playload);

            $playload->getTmpBlog()->addArticle($article);
            $progress->advance();
        }

        $progress->finish();
        $playload->getNewSuccessMessage(" success");
        $playload->jumpLine();

        unset($xmlDataFormater);
        return $playload;
    }

    /**
     * @param $article
     * @param $wpArticle
     * @param $playload
     * @return mixed
     */
    private function hydrateArticle(Article $article, $wpArticle, CommandPlayload $playload, $xmlDataFormater)
    {
        $article->setTitle($xmlDataFormater->formatString('title', $wpArticle));
        $article->setSlug($xmlDataFormater->formatString('post_name', $wpArticle));
        $article->setLink($xmlDataFormater->formatString('link', $wpArticle));
        $article->setPubDate($xmlDataFormater->formatDate('pubDate', $wpArticle));
        $article->setCreator($playload->getTmpBlog()->getAuthor($xmlDataFormater->formatString('creator', $wpArticle)));

        if (null != $xmlDataFormater->formatString('excerpt', $wpArticle)) {
            $article->setDescription($xmlDataFormater->formatString('excerpt', $wpArticle));
        } else {
            $article->setDescription($xmlDataFormater->formatString('description', $wpArticle));
        }

        $article->setContent($xmlDataFormater->formatString('content', $wpArticle));
        $article->setPostId($xmlDataFormater->formatInteger('post_id', $wpArticle));
        $article->setPostDate($xmlDataFormater->formatDate('post_date', $wpArticle));
        $article->setPostDateGmt($xmlDataFormater->formatDate('post_date_gmt', $wpArticle));
        $article->setStatus($xmlDataFormater->formatString('status', $wpArticle));

        return $article;
    }

    /**
     * @param $article
     * @param array $typeAttachement
     * @param $xmlDataFormater
     * @return mixed
     */
    private function manageArticleAttachment($article, array  $typeAttachement, $xmlDataFormater)
    {
        foreach ($typeAttachement as $attachment) {
            foreach ($attachment->postmeta as $postMeta) {
                $value = $postMeta->meta_value;
                // should be update for multiple word press widget
                if ($xmlDataFormater->formatString(0, $value) == "grande-image") {
                    if ($article->getPostId() == $xmlDataFormater->formatInteger('post_parent', $attachment)) {
                        $article->setAttachmentUrl($xmlDataFormater->formatString('attachment_url', $attachment));
                    }
                }
            }
        }

        return $article;
    }

    /**
     * @param Article $article
     * @param $wpArticle
     * @param $xmlDataFormater
     * @param $playload
     */
    private function setCategoryAndTag(Article $article, $wpArticle, XmlDataFormater $xmlDataFormater, CommandPlayload $playload)
    {
        foreach ($wpArticle->category as $cat) {
            foreach ($cat->attributes() as $key => $attribute) {
                if ($key == "domain") {
                    $domain = $attribute;
                } elseif ($key == "nicename") {
                    $nicename = $xmlDataFormater->formatString(0, $attribute);
                }
            }

            if ($domain == "post_tag") {
                foreach ($playload->getNewVicBlog()->getTags() as $tag) {
                    if ($tag->getSlug() == $nicename) {
                        $article->addTag($tag);
                    }
                }
            } elseif ($domain == "category") {
                foreach ($playload->getNewVicBlog()->getCategories() as $category) {
                    if ($category->getSlug() == $nicename) {
                        $article->setCategory($category);
                    }
                }
            }

            return $article;
        }
    }
}