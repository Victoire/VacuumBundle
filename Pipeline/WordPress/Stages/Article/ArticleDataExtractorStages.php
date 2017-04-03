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

            $typePost = [];
            $typeAttachement = [];

            foreach ($blog->item as $wpArticle) {
                $postType = $xmlDataFormater->formatString('post_type', $wpArticle);
                if ($postType == "post") {
                    array_push($typePost, $wpArticle);
                } elseif ($postType == "attachment") {
                    array_push($typeAttachement, $wpArticle);
                }
            }

            foreach ($typePost as $wpArticle) {
                $article = new Article();
                $article->setTitle($xmlDataFormater->formatString('title', $wpArticle));
                $article->setSlug($xmlDataFormater->formatString('post_name', $wpArticle));
                $article->setLink($xmlDataFormater->formatString('link', $wpArticle));
                $article->setPubDate($xmlDataFormater->formatDate('pubDate', $wpArticle));
                $article->setCreator($playload->getAuthor($xmlDataFormater->formatString('creator', $wpArticle)));
                $article->setDescription($xmlDataFormater->formatString('excerpt', $wpArticle));
                $article->setContent($xmlDataFormater->formatString('content', $wpArticle));
                $article->setPostId($xmlDataFormater->formatInteger('post_id', $wpArticle));
                $article->setPostDate($xmlDataFormater->formatDate('post_date', $wpArticle));
                $article->setPostDateGmt($xmlDataFormater->formatDate('post_date_gmt', $wpArticle));
                $article->setStatus($xmlDataFormater->formatString('status', $wpArticle));

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

                foreach ($wpArticle->category as $cat) {
                    foreach ($cat->attributes() as $key => $attribute) {
                        if ($key == "domain") {
                            $domain = $attribute;
                        } elseif ($key == "nicename") {
                            $nicename = $xmlDataFormater->formatString(0, $attribute);
                        }
                    }

                    if ($domain == "post_tag") {
                        foreach ($playload->getNewBlog()->getTags() as $tag) {
                            if ($tag->getSlug() == $nicename) {
                                $article->addTag($tag);
                            }
                        }
                    } elseif ($domain == "category") {
                        foreach ($playload->getNewBlog()->getCategories() as $category) {
                            if ($category->getSlug() == $nicename) {
                                $article->setCategory($category);
                            }
                        }
                    }
                }
                $playload->addItem($article);
            }
        }

        unset($xmlDataFormater);
        return $playload;
    }
}