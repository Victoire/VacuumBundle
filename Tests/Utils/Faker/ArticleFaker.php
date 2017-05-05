<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Utils\Faker;

use Victoire\Bundle\BlogBundle\Entity\ArticleTranslation;
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
    public function generateWPArticles($nb, $tmpBlog, $newVicBlog = null, $content = false)
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
            if ($content) {
                $article->setContent(trim('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pulvinar eros blandit nisi tincidunt, quis finibus odio porta. Mauris non orci risus.<a href="http://www.blogtest.com/article-test-1/2016/12/image-test-1/" rel="attachment wp-att-4547"><img class="aligncenter size-full wp-image-4547" src="http://lorempixel.com/300/200/abstract" alt="image-test-1" width="800" height="350" /></a>Interdum et malesuada fames ac ante'));
            }
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

    /**
     * @param $nb
     * @param $vicBlog
     */
    public function generateVicArticle($nb, $vicBlog)
    {
        for ($ii = 1; $ii < $nb+1; $ii++) {
            $article = new \Victoire\Bundle\BlogBundle\Entity\Article();
            $article->setStatus("published");
            $article->setPublishedAt(new \DateTime('Fri, 04 May 2012 15:23:33 +0000'));
            $article->setLocale("en");
            $article->setDefaultLocale("en");
            $translation = new ArticleTranslation();
            $translation->setLocale("en");
            $translation->setName("Article Test ".$ii);
            $translation->setSlug("article-test-".$ii);
            $article->addTranslation($translation);
            $article->mergeNewTranslations();
            $vicBlog->addArticle($article);
        }
    }
}
