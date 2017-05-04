<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\Article;

use Victoire\Bundle\MediaBundle\Entity\Media;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\VicArticleAttachmentStages;
use Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\AbstractBaseStagesTests;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\ArticleFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\BlogFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\MockProvider\MediaFormaterMockProvider;

/**
 * Class VicArticleAttachmentStagesTest
 */
class VicArticleAttachmentStagesTest extends AbstractBaseStagesTests
{
    public function testPayloadIntegrity()
    {
        $mediaFormaterMockProvider = new MediaFormaterMockProvider();
        $mediaFormater = $mediaFormaterMockProvider->generateMediaFormaterMock();

        $stage = new VicArticleAttachmentStages($mediaFormater);
        $params = [];
        $xml = file_get_contents('Tests/Resources/xml/empty.xml');

        $blogFaker = new BlogFaker();
        $tmpBlog = $blogFaker->generateWordPressBlog();
        $articleFaker = new ArticleFaker();
        $articleFaker->generateWPArticles(2, $tmpBlog);

        $payload = $this->getFreshPayload($params, $xml, $tmpBlog);

        $payload = call_user_func($stage, $payload);

        foreach ($payload->getTmpBlog()->getArticles() as $article) {
            $this->assertInstanceOf(Media::class, $article->getAttachment());
        }
    }
}