<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog\BlogDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\BlogFaker;

/**
 * Class BlogDataExtractorStagesTest.
 */
class BlogDataExtractorStagesTest extends AbstractBaseStagesTests
{
    public function testPayloadIntegrity()
    {
        $stage = new BlogDataExtractorStages();
        $params = ['blog_name' => 'Test Blog'];
        $xml = file_get_contents('Tests/Resources/xml/blog/blog_data_extraction.xml');
        $payload = $this->getFreshPayload($params, $xml, new Blog());

        $payload = call_user_func($stage, $payload);

        $blogProvider = new BlogFaker();
        $expected = $blogProvider->generateWordPressBlog();

        $this->assertEquals($payload->getTmpBlog(), $expected);
    }

    public function testTooManyBlogError()
    {
        $stage = new BlogDataExtractorStages();
        $params = ['blog_name' => 'Test Blog'];
        $xml = file_get_contents('Tests/Resources/xml/blog/wrong_blog_data_extraction.xml');
        $payload = $this->getFreshPayload($params, $xml, new Blog());

        try {
            call_user_func($stage, $payload);
        } catch (\Throwable $e) {
            $this->assertEquals('Dump has more than on blog in it.', $e->getMessage());
        } catch (\Exception $e) {
            $this->assertEquals('Dump has more than on blog in it.', $e->getMessage());
        }
    }
}
