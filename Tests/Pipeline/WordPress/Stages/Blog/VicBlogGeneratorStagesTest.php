<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages;

use Victoire\Bundle\PageBundle\Entity\Page;
use Victoire\Bundle\PageBundle\Repository\PageRepository;
use Victoire\Bundle\TemplateBundle\Entity\Template;
use Victoire\Bundle\TemplateBundle\Repository\TemplateRepository;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog\VicBlogGeneratorStages;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\BlogFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\MockProvider\DoctrineMockProvider;

/**
 * Class VicBlogGeneratorStagesTest.
 */
class VicBlogGeneratorStagesTest extends AbstractBaseStagesTests
{
    public function testPayloadIntegrity()
    {
        $doctrineMockProvider = new DoctrineMockProvider();

        $template = new Template();
        $page = new Page();

        $repositoryReturnValue = [
            0 => [
                'entityName'          => 'VictoireTemplateBundle:Template',
                'entityClass'         => TemplateRepository::class,
                'entityMethod'        => 'find',
                'entityExpectedValue' => $template,
            ],
            1 => [
                'entityName'          => 'VictoirePageBundle:Page',
                'entityClass'         => PageRepository::class,
                'entityMethod'        => 'find',
                'entityExpectedValue' => $page,
            ],
        ];

        $entityManager = $doctrineMockProvider->getEMMock($repositoryReturnValue);
        $stage = new VicBlogGeneratorStages($entityManager);
        $xml = file_get_contents('Tests/Resources/xml/empty.xml');
        $params = [
            'blog_name'      => 'blog test',
            'blog_template'  => 1,
            'blog_parent_id' => 12,
        ];

        $blogProvider = new BlogFaker();

        $payload = $this->getFreshPayload($params, $xml, $blogProvider->generateWordPressBlog());

        $payload = call_user_func($stage, $payload);

        $expected = $blogProvider->getNewVicBlog($template, $page);

        $payload->getNewVicBlog()->setCreatedAt($expected->getCreatedAt());

        $this->assertEquals($expected, $payload->getNewVicBlog());
    }
}
