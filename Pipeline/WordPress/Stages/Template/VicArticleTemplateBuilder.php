<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Template;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\ArticleTemplate;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;
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
     * @param $payload
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $payload->getOutput()->write('Victoire Article Template generation:');

        $parameters = $payload->getParameters();

        if ($parameters['new_article_template']) {
            $template = new ArticleTemplate();
            $template->setName("{{item.name}}", $payload->getNewVicBlog()->getDefaultLocale());
            $template->setSlug("{{item.slug}}", $payload->getNewVicBlog()->getDefaultLocale());
            $template->setBusinessEntityId("article");
            $template->setBackendName($parameters['article_template_name']);
            $template->setLayout($parameters['article_template_layout']);
            $template->setParent($payload->getNewVicBlog());
            $template->setTemplate(self::getTemplate($parameters['article_template_parent_id']));

            foreach ($template->getTranslations() as $key => $translation) {
                if ($key != $payload->getNewVicBlog()->getDefaultLocale()) {
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

        foreach ($payload->getNewVicBlog()->getArticles() as $article) {
            if (null == $article->getTemplate()) {
                $article->setTemplate($template);
            }
        }

        $payload->addParameter("article_content_widget_map", $widgetMapCKEditor);

        $payload->getNewSuccessMessage(" success");
        $payload->jumpLine();
        return $payload;
    }
}