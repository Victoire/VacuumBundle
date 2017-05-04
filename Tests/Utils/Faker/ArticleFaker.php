<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Utils\Faker;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Article;

/**
 * Class ArticleFaker.
 */
class ArticleFaker
{
    /**
     * @param $nb
     * @param $tmpBlog
     */
    public function generateWPArticles($nb, $tmpBlog, $newVicBlog = null)
    {
        for ($ii = 1; $ii < $nb + 1; $ii++) {
            $article = new Article();
            $article->setTitle('Article Test '.$ii);
            $article->setSlug('article-test-'.$ii);
            $article->setLink('http://www.blogtest.com/article-test-'.$ii);
            $article->setPubDate(new \DateTime('Fri, 04 May 2012 15:23:33 +0000'));
            $article->setPostId($ii);
            $article->setPostDate(new \DateTime('2012-05-04 17:23:33'));
            $article->setPostDateGmt(new \DateTime('2012-05-04 15:23:33'));
            $article->setStatus('publish');
            $article->setAttachmentUrl('http://lorempixel.com/300/200/abstract');
            if (null != $newVicBlog) {
                foreach ($newVicBlog->getTags() as $tag) {
                    $article->addTag($tag);
                }
                $article->setCategory($newVicBlog->getCategories()[0]);
            }
            $article->setId($ii);
            $article->setXmlTag('article');
            $tmpBlog->addArticle($article);
        }
    }
}
