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
        foreach ($playload->getItems() as $plArticle) {
            $article = new Article();
            $article->setName($plArticle->getTitle());
            $playload->getNewBlog()->addArticle($article);

            $this->entityManager->persist($article);
        }

        return $playload;
    }
}