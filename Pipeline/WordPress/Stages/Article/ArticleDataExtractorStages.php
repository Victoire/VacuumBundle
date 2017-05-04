<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Article;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class ArticleDataExtractorStages.
 */
class ArticleDataExtractorStages implements StageInterface
{
    /**
     * @param $payload
     *
     * @return mixed
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $xmlDataFormater = new XmlDataFormater();

        $typePost = [];
        $typeAttachement = [];

        $channel = $payload->getRawData()->channel;

        foreach ($channel->item as $wpArticle) {
            $postType = $xmlDataFormater->formatString('post_type', $wpArticle);
            if ($postType == 'post') {
                array_push($typePost, $wpArticle);
            } elseif ($postType == 'attachment') {
                array_push($typeAttachement, $wpArticle);
            }
        }

        $progress = $payload->getNewProgressBar(count($typePost));
        $payload->getNewStageTitleMessage('Article Data extraction:');

        foreach ($typePost as $wpArticle) {
            $article = new Article();

            $article = self::hydrateArticle($article, $wpArticle, $payload, $xmlDataFormater);
            $article = self::manageArticleAttachment($article, $typeAttachement, $xmlDataFormater);
            $article = self::setCategoryAndTag($article, $wpArticle, $xmlDataFormater, $payload);

            $payload->getTmpBlog()->addArticle($article);
            $progress->advance();
        }

        $progress->finish();
        $payload->getNewSuccessMessage(' success');
        $payload->jumpLine();

        unset($xmlDataFormater);

        return $payload;
    }

    /**
     * @param $article
     * @param $wpArticle
     * @param $payload
     *
     * @return mixed
     */
    private function hydrateArticle(Article $article, $wpArticle, CommandPayloadInterface $payload, XmlDataFormater $xmlDataFormater)
    {
        $article->setId($xmlDataFormater->formatInteger('post_id', $wpArticle));
        $article->setXmlTag('article');
        $article->setTitle($xmlDataFormater->formatString('title', $wpArticle));
        $article->setSlug($xmlDataFormater->formatString('post_name', $wpArticle));
        $article->setLink($xmlDataFormater->formatString('link', $wpArticle));
        $article->setPubDate($xmlDataFormater->formatDate('pubDate', $wpArticle));
        $article->setCreator($payload->getTmpBlog()->getAuthor($xmlDataFormater->formatString('creator', $wpArticle)));

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
     *
     * @return mixed
     */
    private function manageArticleAttachment($article, array  $typeAttachement, $xmlDataFormater)
    {
        foreach ($typeAttachement as $attachment) {
            foreach ($attachment->postmeta as $postMeta) {
                $value = $postMeta->meta_value;
                // should be update for multiple word press widget
                if ($xmlDataFormater->formatString(0, $value) == 'grande-image') {
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
     * @param XmlDataFormater         $xmlDataFormater
     * @param CommandPayloadInterface $payload
     *
     * @return Article
     */
    private function setCategoryAndTag(Article $article, $wpArticle, XmlDataFormater $xmlDataFormater, CommandPayloadInterface $payload)
    {
        foreach ($wpArticle->category as $cat) {
            foreach ($cat->attributes() as $key => $attribute) {
                if ($key == 'domain') {
                    $domain = $attribute;
                } elseif ($key == 'nicename') {
                    $nicename = $xmlDataFormater->formatString(0, $attribute);
                }
            }

            if ($domain == 'post_tag') {
                foreach ($payload->getNewVicBlog()->getTags() as $tag) {
                    if ($tag->getSlug() == $nicename) {
                        $article->addTag($tag);
                    }
                }
            } elseif ($domain == 'category') {
                foreach ($payload->getNewVicBlog()->getCategories() as $category) {
                    if ($category->getSlug() == $nicename) {
                        $article->setCategory($category);
                    }
                }
            }
        }

        return $article;
    }
}
