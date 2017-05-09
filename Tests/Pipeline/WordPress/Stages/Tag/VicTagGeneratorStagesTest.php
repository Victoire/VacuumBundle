<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\Tag;

use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag\VicTagGeneratorStages;
use Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\AbstractBaseStagesTests;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\BlogFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\TagFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\MockProvider\DoctrineMockProvider;

/**
 * Class VicTagGeneratorStagesTest.
 */
class VicTagGeneratorStagesTest extends AbstractBaseStagesTests
{
    public function testPayloadIntegrity()
    {
        $doctrineProvider = new DoctrineMockProvider();
        $entityManager = $doctrineProvider->getEMMock();

        $stage = new VicTagGeneratorStages($entityManager);

        $xml = file_get_contents('Tests/Resources/xml/empty.xml');
        $params = [];
        $blogProvider = new BlogFaker();
        $tmpBlog = $blogProvider->generateWordPressBlog();
        $tagFaker = new TagFaker();
        $tagFaker->generateWPTag(5, $tmpBlog);

        $payload = $this->getFreshPayload($params, $xml, $tmpBlog);

        $payload->setNewVicBlog($blogProvider->getNewVicBlog());

        $payload = call_user_func($stage, $payload);

        $expected = $blogProvider->getNewVicBlog();
        $tagFaker->generateVicTag(5, $expected);

        $this->assertEquals($expected, $payload->getNewVicBlog());
    }
}
