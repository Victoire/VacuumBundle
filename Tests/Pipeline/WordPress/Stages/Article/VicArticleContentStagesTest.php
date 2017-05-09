<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\Article;

use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article\VicArticleContentStages;
use Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\AbstractBaseStagesTests;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\ArticleFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\BlogFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\MockProvider\MediaFormaterMockProvider;

/**
 * Class VicArticleContentStagesTest.
 */
class VicArticleContentStagesTest extends AbstractBaseStagesTests
{
    public function testPayloadIntegrity()
    {
        $mediaFormaterMockProvider = new MediaFormaterMockProvider();
        $stage = new VicArticleContentStages($mediaFormaterMockProvider->generateMediaFormaterMock());

        $params = [];
        $xml = file_get_contents('Tests/Resources/xml/empty.xml');
        $blogFaker = new BlogFaker();
        $tmpBlog = $blogFaker->generateWordPressBlog();
        $articleFaker = new ArticleFaker();
        $articleFaker->generateWPArticles(2, $tmpBlog, null, true);

        $payload = $this->getFreshPayload($params, $xml, $tmpBlog);

        $payload = call_user_func($stage, $payload);

        $expected = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pulvinar eros blandit nisi tincidunt, quis finibus odio porta. Mauris non orci risus.<a href="http://www.blogtest.com/article-test-1/2016/12/image-test-1/" rel="attachment wp-att-4547"><img class="aligncenter size-full wp-image-4547" src="/uploads/media/test-blog/abstract" alt="image-test-1" width="800" height="350"></a>Interdum et malesuada fames ac ante</p>';

        foreach ($payload->getTmpBlog()->getArticles() as $key => $articles) {
            $expectedContent = self::formatStringPresentation($expected);
            $actualContent = self::formatStringPresentation($articles->getContent());
            $this->assertEquals($expectedContent, $actualContent);
        }
    }

    /**
     * @param $string
     *
     * @return string
     */
    private function formatStringPresentation($string)
    {
        return preg_replace('/\s/', '', $string);
    }
}
