<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Template;

use Victoire\Bundle\BlogBundle\Entity\ArticleTemplate;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\Widget\CKEditorBundle\Entity\WidgetCKEditor;
use Victoire\Widget\LayoutBundle\Entity\WidgetLayout;

/**
 * Class VicArticleTemplateBuilder
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Template
 */
class VicArticleTemplateBuilder implements StageInterface
{
    /**
     * @param $playload
     */
    public function __invoke($playload)
    {
        $template = new ArticleTemplate();
        $template->setName("blog quovadis article template");
        $template->setBusinessEntityId("article");
        $template->setBackendName("quovadis-article-template");

        $widgetMapLayout = new WidgetMap();
        $widgetMapLayout->setAction(WidgetMap::ACTION_CREATE);
        $widgetMapLayout->setSlot("main_content");

        $widgetLayout = new WidgetLayout();
        $widgetLayout->setWidgetMap($widgetMapLayout);
        $widgetLayout->setLayoutMd("once");
        $widgetLayout->setHasContainer(true);

        $widgetMapCKEditor = new WidgetMap();
        $widgetMapCKEditor->setAction(WidgetMap::ACTION_CREATE);
        $widgetMapCKEditor->setSlot("static_ckeditor");

        $widgetCKEditor = new WidgetCKEditor();
        $widgetCKEditor->setWidgetMap($widgetMapCKEditor);

        $template->addWidgetMap($widgetMapLayout);
        $template->addWidgetMap($widgetMapCKEditor);

        $template->setParent($playload->getNewBlog());

        foreach ($playload->getNewBlog()->getArticles() as $article) {
            $article->setTemplate($template);
        }

        return $playload;
    }
}