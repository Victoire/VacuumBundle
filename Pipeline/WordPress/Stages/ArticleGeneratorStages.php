<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages;

use Victoire\Bundle\BlogBundle\Entity\Article;
use Victoire\DevTools\VacuumBundle\Entity\Playload;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;

/**
 * Class ArticleGeneratorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\Stages
 */
class ArticleGeneratorStages implements StageInterface
{
    public function __invoke(Playload $playload)
    {
        foreach ($playload->getResult() as $key => $result) {
            $article = new Article();
            $result->setArticle($article);
        }
        return $playload;
    }
}