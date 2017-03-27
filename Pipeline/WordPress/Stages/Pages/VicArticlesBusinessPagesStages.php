<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Pages;

use Victoire\Bundle\BusinessPageBundle\Entity\BusinessPage;
use Victoire\Bundle\PageBundle\Entity\Page;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\Widget\CKEditorBundle\Entity\WidgetCKEditor;

class VicArticlesBusinessPagesStages implements StageInterface
{
    public function __invoke($playload)
    {
        $overWriteWidgetMaps = $playload->getNewBlog()->getTemplate()->getWidgetMaps();
        foreach ($overWriteWidgetMaps as $widgetMap) {
            if ($widgetMap->getSlot() == "static_ckeditor") {
                $overWriteWidgetMap = $widgetMap;
            }
        }

        foreach ($playload->getNewBlog()->getArticles() as $key => $article) {

            $entityProxy = new EntityProxy();
            $entityProxy->setEntity($article, $article->getName());

            $widgetMapCKEditor = new WidgetMap();
            $widgetMapCKEditor->setAction(WidgetMap::ACTION_OVERWRITE);
            $widgetMapCKEditor->setReplaced($overWriteWidgetMap);

            $widgetCKEditor = new WidgetCKEditor();
            $widgetCKEditor->setWidgetMap($widgetMapCKEditor);

            $businessPage = new BusinessPage();
            $businessPage->setTemplate($article->getTemplate());
            $businessPage->setParent($playload->getNewBlog());
            $businessPage->addWidgetMap($widgetMapCKEditor);
            $businessPage->setEntityProxy($entityProxy);
            $businessPage->setStatus("published");
        }

        return $playload;
    }
}