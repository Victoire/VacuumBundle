<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Pages;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BusinessPageBundle\Entity\BusinessPage;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;
use Victoire\Widget\CKEditorBundle\Entity\WidgetCKEditor;
use Victoire\Bundle\CoreBundle\Entity\EntityProxy;

/**
 * Class VicArticlesBusinessPagesStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Pages
 */
class VicArticlesBusinessPagesStages implements PersisterStageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * VicArticlesBusinessPagesStages constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $playload CommandPlayloadInterface
     * @return mixed
     */
    public function __invoke(CommandPlayloadInterface $playload)
    {
        $progress = $playload->getNewProgressBar(count($playload->getNewVicBlog()->getArticles()));
        $playload->getNewStageTitleMessage('Victoire BusinessPage generation:');

        foreach ($playload->getNewVicBlog()->getArticles() as  $article) {

            $overWriteWidgetMap = $playload->getParameter('article_content_widget_map');

            $entityProxy = new EntityProxy();
            $entityProxy->setEntity($article, "article");
            $this->entityManager->persist($entityProxy);

            $widgetMapCKEditor = new WidgetMap();
            $widgetMapCKEditor->setAction(WidgetMap::ACTION_OVERWRITE);
            $widgetMapCKEditor->setReplaced($overWriteWidgetMap);
            $widgetMapCKEditor->setSlot($overWriteWidgetMap->getSlot());

            $widgetCKEditor = new WidgetCKEditor();
            $widgetCKEditor->setWidgetMap($widgetMapCKEditor);

            foreach ($playload->getTmpBlog()->getArticles() as $wpArticle) {
                if ($wpArticle->getTitle() == $article->getName()) {
                    $widgetCKEditor->setContent($wpArticle->getContent());
                }
            }

            $businessPage = new BusinessPage();
            $businessPage->setTemplate($article->getTemplate());
            $businessPage->setName($article->getName(), $playload->getNewVicBlog()->getCurrentLocale());
            $businessPage->setslug($article->getslug(), $playload->getNewVicBlog()->getCurrentLocale());

            foreach ($businessPage->getTranslations() as $key => $translation) {
                if ($key != $playload->getNewVicBlog()->getCurrentLocale()) {
                    $businessPage->removeTranslation($translation);
                }
            }

            $businessPage->setParent($playload->getNewVicBlog());
            $businessPage->addWidgetMap($widgetMapCKEditor);
            $businessPage->setEntityProxy($entityProxy);
            $businessPage->setStatus("published");

            $this->entityManager->persist($businessPage);
            $progress->advance();
        }

        $this->entityManager->persist($playload->getNewVicBlog());
        $progress->finish();

        $playload->getNewSuccessMessage(" success");
        $playload->jumpLine();

        return $playload;
    }
}