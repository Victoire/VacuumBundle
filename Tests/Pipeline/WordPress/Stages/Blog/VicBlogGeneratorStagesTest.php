<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages;

use Victoire\Bundle\I18nBundle\Entity\ViewTranslation;
use Victoire\Bundle\PageBundle\Entity\Page;
use Victoire\Bundle\PageBundle\Repository\PageRepository;
use Victoire\Bundle\TemplateBundle\Entity\Template;
use Victoire\Bundle\TemplateBundle\Repository\TemplateRepository;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog\VicBlogGeneratorStages;
use Victoire\DevTools\VacuumBundle\Tests\Utils\DoctrineMockProvider;

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

        $blogDataExtractorTest = new BlogDataExtractorStagesTest();

        $payload = $this->getFreshPayload($params, $xml, $blogDataExtractorTest->generateBaseBlog());

        $payload = call_user_func($stage, $payload);

        $expected = new \Victoire\Bundle\BlogBundle\Entity\Blog();
        $expected->setCurrentLocale('en');
        $expected->setDefaultLocale('en');
        $expected->setStatus('published');
        $expected->setPublishedAt(new \DateTime('Tue, 02 May 2017 13:56:23 +0000'));
        $translation = new ViewTranslation();
        $translation->setLocale('en');
        $translation->setName('blog test');
        $translation->setTranslatable($expected);
        $expected->addTranslation($translation);
        $expected->setTemplate($template);
        $expected->setParent($page);
        $expected->setCreatedAt(new \DateTime('now'));
        $expected->mergeNewTranslations();

        $payload->getNewVicBlog()->setCreatedAt($expected->getCreatedAt());

        $this->assertEquals($expected, $payload->getNewVicBlog());
    }
}
