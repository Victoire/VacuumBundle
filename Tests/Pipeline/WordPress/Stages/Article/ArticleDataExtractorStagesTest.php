<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\Article;

use Victoire\Bundle\UserBundle\Entity\User;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\ArticleDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\AbstractBaseStagesTests;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\ArticleFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\BlogFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\CategoryFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\TagFaker;

/**
 * Class ArticleDataExtractorStagesTest.
 */
class ArticleDataExtractorStagesTest extends AbstractBaseStagesTests
{
    public function testPayloadIntegrity()
    {
        $stage = new ArticleDataExtractorStages();
        $params = [];
        $xml = file_get_contents('Tests/Resources/xml/article/article_data_extraction.xml');

        $tmpBlog = new Blog();
        $author = new User();
        $author->setUsername('author1');

        $payload = $this->getFreshPayload($params, $xml, $tmpBlog);

        $blogFaker = new BlogFaker();
        $newVicBlog = $blogFaker->getNewVicBlog();
        $tagFaker = new TagFaker();
        $categoryFaker = new CategoryFaker();
        $tagFaker->generateVicTag(3, $newVicBlog);
        $categoryFaker->generateVictoireCategory(1, $newVicBlog);

        $payload->setNewVicBlog($newVicBlog);

        $payload = call_user_func($stage, $payload);

        $expected = $blogFaker->generateWordPressBlog();
        $tagFaker->generateVicTag(3, $expected);
        $articleFaker = new ArticleFaker();
        $articleFaker->generateWPArticles(2, $expected, $newVicBlog);

        foreach ($payload->getTmpBlog()->getArticles() as $key => $articles) {
            $this->assertEquals($expected->getArticles()[$key], $articles);
        }
    }
}
