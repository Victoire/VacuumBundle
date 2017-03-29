<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Template;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\ArticleTemplate;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\Widget\CKEditorBundle\Entity\WidgetCKEditor;
use Victoire\Widget\LayoutBundle\Entity\WidgetLayout;

/**
 * Class VicArticleTemplateBuilder
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Template
 */
class VicArticleTemplateBuilder implements PersisterStageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * VicArticleTemplateBuilder constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $id
     * @return null|object|\Victoire\Bundle\TemplateBundle\Entity\Template
     */
    private function getBaseTemplate($id)
    {
        $template = $this->entityManager->getRepository('VictoireTemplateBundle:Template')->find($id);
        return $template;
    }

    /**
     * @param $playload
     */
    public function __invoke($playload)
    {
        $template = new ArticleTemplate();
        $template->setName("{{item.name}}", $playload->getLocale());
        $template->setSlug("{{item.slug}}", $playload->getLocale());
        $template->setBusinessEntityId("article");
        $template->setBackendName("quovadis-article-template");
        $template->setLayout("one_col");
        $template->setParent($playload->getNewBlog());
        $template->setTemplate(self::getBaseTemplate(1));

        foreach ($template->getTranslations() as $key => $translation) {
            if ($key != $playload->getLocale()) {
                $template->removeTranslation($translation);
            }
        }

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

        foreach ($playload->getNewBlog()->getArticles() as $article) {
            $article->setTemplate($template);
        }

        return $playload;
    }
}