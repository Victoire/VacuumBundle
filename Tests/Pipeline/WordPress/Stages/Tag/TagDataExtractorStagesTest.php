<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\Tag;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\Tag;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag\TagDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\AbstractBaseStagesTests;

/**
 * Class TagDataExtractorStagesTest
 * @package Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\Tag
 */
class TagDataExtractorStagesTest extends AbstractBaseStagesTests
{
    public function testPayloadIntegrity()
    {
        $stage = new TagDataExtractorStages();
        $params = [];
        $xml = file_get_contents('Tests/Resources/xml/tag/tag_data_extraction.xml');
        $payload = $this->getFreshPayload($params, $xml, new Blog());

        $payload = call_user_func($stage, $payload);

        $tmpBlog = new Blog();
        for ($ii = 1; $ii < 6; $ii++) {
            $tag = new Tag();
            $tag->setTagName('Test'.$ii);
            $tag->setTagSlug('test-tag'.$ii);
            $tag->setXmlTag('tag');
            $tag->setId($ii);
            $tmpBlog->addTag($tag);
        }

        foreach ($tmpBlog->getTags() as $key => $tag) {
            $expectedTag = $payload->getTmpBlog()->getTags()[$key];
            $this->assertEquals($tag, $expectedTag);
        }
    }
}
