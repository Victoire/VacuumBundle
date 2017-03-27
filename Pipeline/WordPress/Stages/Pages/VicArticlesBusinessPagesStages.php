<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Pages;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BusinessPageBundle\Entity\BusinessPage;
use Victoire\Bundle\PageBundle\Entity\Page;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStagesInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\Widget\CKEditorBundle\Entity\WidgetCKEditor;
use Victoire\Bundle\CoreBundle\Entity\EntityProxy;

class VicArticlesBusinessPagesStages implements StageInterface
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke($playload)
    {
        foreach ($playload->getNewBlog()->getArticles() as $key => $article) {

            $overWriteWidgetMaps = $article->getTemplate()->getWidgetMaps();
            foreach ($overWriteWidgetMaps as $widgetMap) {
                if ($widgetMap->getSlot() == "static_ckeditor") {
                    $overWriteWidgetMap = $widgetMap;
                }
            }

            $entityProxy = new EntityProxy();
            $entityProxy->setEntity($article, "article");
//            $this->entityManager->persist($entityProxy);

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

            $this->entityManager->persist($businessPage);
        }

        $this->entityManager->persist($playload->getNewBlog());

        return $playload;
    }
}