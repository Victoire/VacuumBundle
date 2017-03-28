<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Article;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStagesInterface;

/**
 * Class VicArticleGeneratorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article
 */
class VicArticleGeneratorStages implements PersisterStagesInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * VicArticleGeneratorStages constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $playload
     * @return mixed
     */
    public function __invoke($playload)
    {
        $locale = $playload->getLocale();

        foreach ($playload->getItems() as $plArticle) {
            if (null != $plArticle->getTitle()) {
                $article = new Article();
                $article->setName($plArticle->getTitle(), $locale);
                $article->setDescription($plArticle->getDescription());
                $article->setPublishedAt($plArticle->getPubDate());
                $article->setLocale($locale);
                $playload->getNewBlog()->addArticle($article);

                foreach ($article->getTranslations() as $key => $translation) {
                    if ($key != $locale) {
                        $article->removeTranslation($translation);
                    }
                }

                $this->entityManager->persist($article);
            }
        }

        return $playload;
    }
}