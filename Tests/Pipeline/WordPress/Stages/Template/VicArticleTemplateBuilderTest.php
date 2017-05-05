<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\Template;

use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Template\VicArticleTemplateBuilder;
use Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\AbstractBaseStagesTests;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\ArticleFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\BlogFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\CategoryFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\TagFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\TemplateFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\WidgetFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\MockProvider\DoctrineMockProvider;

/**
 * Class VicArticleTemplateBuilderTest.
 */
class VicArticleTemplateBuilderTest extends AbstractBaseStagesTests
{
    public function testPayloadIntegrity()
    {
        $doctrineMockerProvider = new DoctrineMockProvider();
        $stage = new VicArticleTemplateBuilder($doctrineMockerProvider->getEMMock());

        $params = [
            'new_article_template'        => true,
            'article_template_name'       => 'blog_test_article_template',
            'article_template_layout'     => 'oneCol_test_layout',
            'article_template_parent_id'  => 1,
            'article_template_first_slot' => 'main_content',
        ];
        $xml = file_get_contents('Tests/Resources/xml/empty.xml');

        $blogFaker = new BlogFaker();
        $newVicBlog = $blogFaker->getNewVicBlog();

        $articleFaker = new ArticleFaker();
        $articleFaker->generateVicArticle(2, $newVicBlog);

        $categoryFaker = new CategoryFaker();
        $tagFaker = new TagFaker();

        foreach ($newVicBlog->getArticles() as $article) {
            $article->setTags($tagFaker->generateVicTag(3));
            $article->setCategory($categoryFaker->getOneVicCategory(1));
        }

        $payload = $this->getFreshPayload($params, $xml);

        $payload->setNewVicBlog($newVicBlog);

        $payload = call_user_func($stage, $payload);

        $expected = $blogFaker->getNewVicBlog();

        $articleFaker->generateVicArticle(2, $expected);

        $tagFaker = new TagFaker();

        foreach ($expected->getArticles() as $article) {
            $article->setTags($tagFaker->generateVicTag(3));
            $article->setCategory($categoryFaker->getOneVicCategory(1));
        }

        $templateFaker = new TemplateFaker();
        $widgetFaker = new WidgetFaker();
        $template = $templateFaker->generateVicArticleTemplate($expected, $widgetFaker->generateWidget());

        foreach ($expected->getArticles() as $key => $article) {
            $article->setTemplate($template);
        }

        foreach ($expected->getArticles() as $artKey => $article) {
            $actualArticle = $payload->getNewVicBlog()->getArticles()[$artKey]->getTemplate();
            foreach ($article->getTemplate()->getWidgetMaps() as $widgMapKey => $widgetMap) {
                $actualWidgetMap = $actualArticle->getWidgetMaps()[$widgMapKey];
                $widgetMap->setSlot($actualWidgetMap->getSlot());

                foreach ($widgetMap->getWidgets() as $widgKey => $widget) {
                    $actualWidget = $actualWidgetMap->getWidgets()[$widgKey];
                    $widget->setChildrenSlot($actualWidget->getChildrenSlot());
                }
            }
        }

        foreach ($expected->getArticles() as $key => $article) {
            $this->assertEquals($article->getTemplate(), $payload->getNewVicBlog()->getArticles()[$key]->getTemplate());
        }
    }
}
