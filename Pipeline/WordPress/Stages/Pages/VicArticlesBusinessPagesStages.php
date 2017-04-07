<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Pages;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BusinessPageBundle\Entity\BusinessPage;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;
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
     * @param $payload CommandPayloadInterface
     * @return mixed
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $progress = $payload->getNewProgressBar(count($payload->getNewVicBlog()->getArticles()));
        $payload->getNewStageTitleMessage('Victoire BusinessPage generation:');

        foreach ($payload->getNewVicBlog()->getArticles() as  $article) {

            $businessPage = null;

            if (null != $article->getId()) {
                $ep = $this->entityManager
                    ->getRepository('Victoire\Bundle\CoreBundle\Entity\EntityProxy')
                    ->findOneBy(['article' => $article->getId()]);
                if (null != $ep) {
                    $businessPage = $this->entityManager->getRepository('VictoireBusinessPageBundle:BusinessPage')
                        ->findOneBy(['entityProxy' => $ep->getId()]);
                }
            }

            if (null == $businessPage) {
                $overWriteWidgetMap = $payload->getParameter('article_content_widget_map');

                $entityProxy = new EntityProxy();
                $entityProxy->setEntity($article, "article");
                $this->entityManager->persist($entityProxy);

                $widgetMapCKEditor = new WidgetMap();
                $widgetMapCKEditor->setAction(WidgetMap::ACTION_OVERWRITE);
                $widgetMapCKEditor->setReplaced($overWriteWidgetMap);
                $widgetMapCKEditor->setSlot($overWriteWidgetMap->getSlot());

                $widgetCKEditor = new WidgetCKEditor();
                $widgetCKEditor->setWidgetMap($widgetMapCKEditor);

                foreach ($payload->getTmpBlog()->getArticles() as $wpArticle) {
                    if ($wpArticle->getTitle() == $article->getName()) {
                        $widgetCKEditor->setContent($wpArticle->getContent());
                    }
                }

                $businessPage = new BusinessPage();
                $businessPage->setTemplate($article->getTemplate());
                $businessPage->setName($article->getName(), $payload->getNewVicBlog()->getCurrentLocale());
                $businessPage->setslug($article->getslug(), $payload->getNewVicBlog()->getCurrentLocale());

                foreach ($businessPage->getTranslations() as $key => $translation) {
                    if ($key != $payload->getNewVicBlog()->getCurrentLocale()) {
                        $businessPage->removeTranslation($translation);
                    }
                }

                $businessPage->setParent($payload->getNewVicBlog());
                $businessPage->addWidgetMap($widgetMapCKEditor);
                $businessPage->setEntityProxy($entityProxy);
                $businessPage->setStatus("published");

                $this->entityManager->persist($businessPage);
                $progress->advance();
            }
        }

        $this->entityManager->persist($payload->getNewVicBlog());
        $progress->finish();

        $payload->getNewSuccessMessage(" success");
        $payload->jumpLine();

        return $payload;
    }
}