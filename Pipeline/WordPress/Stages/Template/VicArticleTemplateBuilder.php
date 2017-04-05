<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Template;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\ArticleTemplate;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;
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
    private function getTemplate($id)
    {
        $template = $this->entityManager->getRepository('VictoireTemplateBundle:Template')->find($id);
        return $template;
    }

    /**
     * Create a new ArticleTemplate with a widget Layout
     * and a widget CKEditor in it.
     *
     * @param $playload
     */
    public function __invoke(CommandPlayloadInterface $playload)
    {
        $playload->getOutput()->write('Victoire Article Template generation:');

        $parameters = $playload->getParameters();

        if ($parameters['new_article_template']) {
            $template = new ArticleTemplate();
            $template->setName("{{item.name}}", $playload->getNewVicBlog()->getDefaultLocale());
            $template->setSlug("{{item.slug}}", $playload->getNewVicBlog()->getDefaultLocale());
            $template->setBusinessEntityId("article");
            $template->setBackendName($parameters['article_template_name']);
            $template->setLayout($parameters['article_template_layout']);
            $template->setParent($playload->getNewVicBlog());
            $template->setTemplate(self::getTemplate($parameters['article_template_parent_id']));

            foreach ($template->getTranslations() as $key => $translation) {
                if ($key != $playload->getNewVicBlog()->getDefaultLocale()) {
                    $template->removeTranslation($translation);
                }
            }
        } else {
            $template = self::getTemplate($parameters['article_template_id']);
        }

        $widgetMapLayout = new WidgetMap();
        $widgetMapLayout->setAction(WidgetMap::ACTION_CREATE);
        $widgetMapLayout->setSlot($parameters['article_template_first_slot']);

        $widgetLayout = new WidgetLayout();
        $widgetLayout->setWidgetMap($widgetMapLayout);
        $widgetLayout->setLayoutMd("once");
        $widgetLayout->setHasContainer(true);

        $widgetMapCKEditor = new WidgetMap();
        $widgetMapCKEditor->setAction(WidgetMap::ACTION_CREATE);
        $widgetMapCKEditor->setSlot($widgetLayout->getChildrenSlot()."_1");

        $widgetCKEditor = new WidgetCKEditor();
        $widgetCKEditor->setWidgetMap($widgetMapCKEditor);

        $template->addWidgetMap($widgetMapLayout);
        $template->addWidgetMap($widgetMapCKEditor);

        foreach ($playload->getNewVicBlog()->getArticles() as $article) {
            $article->setTemplate($template);
        }

        $playload->addParameter("article_content_widget_map", $widgetMapCKEditor);

        $playload->getNewSuccessMessage(" success");
        $playload->jumpLine();
        return $playload;
    }
}