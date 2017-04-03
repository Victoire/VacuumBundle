<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Pages;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BusinessPageBundle\Entity\BusinessPage;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
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
     * @param $playload
     * @return mixed
     */
    public function __invoke(PlayloadInterface $playload)
    {
        $progress = $playload->getProgressBar(count($playload->getNewBlog()->getArticles()));
        $playload->getOutput()->writeln(sprintf('Victoire BusinessPage generation:'));

        foreach ($playload->getNewBlog()->getArticles() as  $article) {

            $overWriteWidgetMap = $playload->getContentWidgetMap();

            $entityProxy = new EntityProxy();
            $entityProxy->setEntity($article, "article");
            $this->entityManager->persist($entityProxy);

            $widgetMapCKEditor = new WidgetMap();
            $widgetMapCKEditor->setAction(WidgetMap::ACTION_OVERWRITE);
            $widgetMapCKEditor->setReplaced($overWriteWidgetMap);
            $widgetMapCKEditor->setSlot($overWriteWidgetMap->getSlot());

            $widgetCKEditor = new WidgetCKEditor();
            $widgetCKEditor->setWidgetMap($widgetMapCKEditor);

            foreach ($playload->getItems() as $wpArticle) {
                if ($wpArticle->getTitle() == $article->getName()) {
                    $widgetCKEditor->setContent($wpArticle->getContent());
                }
            }

            $businessPage = new BusinessPage();
            $businessPage->setTemplate($article->getTemplate());
            $businessPage->setName($article->getName(), $playload->getLocale());
            $businessPage->setslug($article->getslug(), $playload->getLocale());

            foreach ($businessPage->getTranslations() as $key => $translation) {
                if ($key != $playload->getLocale()) {
                    $businessPage->removeTranslation($translation);
                }
            }

            $businessPage->setParent($playload->getNewBlog());
            $businessPage->addWidgetMap($widgetMapCKEditor);
            $businessPage->setEntityProxy($entityProxy);
            $businessPage->setStatus("published");

            $this->entityManager->persist($businessPage);
            $progress->advance();
        }

        $this->entityManager->persist($playload->getNewBlog());
        $progress->finish();
        $playload->getOutput()->writeln(' success');

        return $playload;
    }
}