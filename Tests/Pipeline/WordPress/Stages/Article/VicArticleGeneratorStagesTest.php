<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\Article;

use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\VicArticleGeneratorStages;
use Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\AbstractBaseStagesTests;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\ArticleFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\BlogFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\CategoryFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\TagFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\MockProvider\DoctrineMockProvider;

/**
 * Class VicArticleGeneratorStagesTest.
 */
class VicArticleGeneratorStagesTest extends AbstractBaseStagesTests
{
    public function testPayloadIntegrity()
    {
        $doctrineMockerprovider = new DoctrineMockProvider();
        $stage = new VicArticleGeneratorStages($doctrineMockerprovider->getEMMock());

        $params = [];
        $xml = file_get_contents('Tests/Resources/xml/empty.xml');

        $blogFaker = new BlogFaker();
        $tmpBlog = $blogFaker->generateWordPressBlog();

        $articleFaker = new ArticleFaker();
        $articleFaker->generateWPArticles(2, $tmpBlog, null, true);

        $categoryFaker = new CategoryFaker();
        $tagFaker = new TagFaker();

        foreach ($tmpBlog->getArticles() as $article) {
            $article->setTags($tagFaker->generateVicTag(3));
            $article->setCategory($categoryFaker->getOneVicCategory(1));
        }

        $payload = $this->getFreshPayload($params, $xml, $tmpBlog);

        $newVicBlog = $blogFaker->getNewVicBlog();
        $payload->setNewVicBlog($newVicBlog);

        $payload = call_user_func($stage, $payload);

        $expected = $blogFaker->getNewVicBlog();
        $articleFaker->generateVicArticle(2, $expected);
        foreach ($expected->getArticles() as $article) {
            $article->setTags($tagFaker->generateVicTag(3));
            $article->setCategory($categoryFaker->getOneVicCategory(1));
        }

        foreach ($expected->getArticles() as $key => $article) {
            $this->assertEquals($article, $payload->getNewVicBlog()->getArticles()[$key]);
        }
    }
}
