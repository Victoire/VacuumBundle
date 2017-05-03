<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\Category;

use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category\VicCategoryGeneratorStages;
use Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\AbstractBaseStagesTests;
use Victoire\DevTools\VacuumBundle\Tests\Utils\BlogFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\CategoryFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\DoctrineMockProvider;

/**
 * Class VicCategoryGeneratorStagesTest.
 */
class VicCategoryGeneratorStagesTest extends AbstractBaseStagesTests
{
    public function testPayloadIntegrity()
    {
        $doctrineProvider = new DoctrineMockProvider();
        $entityManager = $doctrineProvider->getEMMock();

        $stage = new VicCategoryGeneratorStages($entityManager);

        $xml = file_get_contents('Tests/Resources/xml/empty.xml');
        $params = [];
        $blogProvider = new BlogFaker();
        $tmpBlog = $blogProvider->generateWordPressBlog();
        $categoryFaker = new CategoryFaker();
        $categoryFaker->generateWPCategories(5, $tmpBlog);

        $payload = $this->getFreshPayload($params, $xml, $tmpBlog);

        $payload->setNewVicBlog($blogProvider->getNewVicBlog());

        $payload = call_user_func($stage, $payload);

        $expected = $blogProvider->getNewVicBlog();
        $categoryFaker->generateVictoireCategory(5, $expected);

        $this->assertEquals($expected, $payload->getNewVicBlog());
    }
}
