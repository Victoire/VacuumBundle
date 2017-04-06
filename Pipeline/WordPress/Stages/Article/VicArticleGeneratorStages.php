<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Article;
use Victoire\Bundle\MediaBundle\Entity\Media;
use Victoire\Bundle\PageBundle\Entity\PageStatus;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;

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
     * @param $payload
     * @return mixed
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $locale = $payload->getNewVicBlog()->getDefaultLocale();

        $progress = $payload->getNewProgressBar(count($payload->getTmpBlog()->getArticles()));
        $payload->getNewStageTitleMessage('Victoire Article generation:');

        foreach ($payload->getTmpBlog()->getArticles() as $plArticle) {

            if (null != $plArticle->getTitle()) {
                $article = new Article();
                $article->setName($plArticle->getTitle(), $locale);
                $article->setSlug($plArticle->getSlug(), $locale);

                if (null != $plArticle->getDescription()) {
                    $article->setDescription($plArticle->getDescription(), $locale);
                }

                $article->setPublishedAt($plArticle->getPubDate());
                if ($plArticle->getStatus() == "publish") {
                    $article->setStatus(PageStatus::PUBLISHED);
                }
                if (null != $plArticle->getAttachment()) {
                    $article->setImage($plArticle->getAttachment(), $locale);
                }
                $article->setLocale($locale);

                if (null != $plArticle->getCategory()) {
                    $article->setCategory($plArticle->getCategory());
                }

                if (null != $plArticle->getTags()) {
                    $article->setTags($plArticle->getTags());
                }

                $article->setAuthor($plArticle->getCreator());

                $payload->getNewVicBlog()->addArticle($article);

                // remove default "en" ArticleTranslation to avoid error when flushing
                foreach ($article->getTranslations() as $key => $translation) {
                    if ($key != $locale) {
                        $article->removeTranslation($translation);
                    }
                }
                $progress->advance();
            }
        }

        $progress->finish();
        $payload->getNewSuccessMessage(" success");
        $payload->jumpLine();

        return $payload;
    }
}