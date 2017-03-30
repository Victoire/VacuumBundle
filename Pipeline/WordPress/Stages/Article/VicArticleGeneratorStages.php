<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Article;
use Victoire\Bundle\MediaBundle\Entity\Media;
use Victoire\Bundle\PageBundle\Entity\PageStatus;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;

/**
 * Class VicArticleGeneratorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article
 */
class VicArticleGeneratorStages implements PersisterStageInterface
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
                $article->setSlug($plArticle->getSlug(), $locale);
                $article->setDescription($plArticle->getDescription(), $locale);
                $article->setPublishedAt($plArticle->getPubDate());
                if ($plArticle->getStatus() == "publish") {
                    $article->setStatus(PageStatus::PUBLISHED);
                }
                if (null != $plArticle->getAttachment()) {
                    $article->setImage($plArticle->getAttachment(), $locale);
                }
                $article->setLocale($locale);

                $playload->getNewBlog()->addArticle($article);

                // remove default "en" ArticleTranslation to avoid error when flushing
                foreach ($article->getTranslations() as $key => $translation) {
                    if ($key != $locale) {
                        $article->removeTranslation($translation);
                    }
                }
            }
        }

        return $playload;
    }
}