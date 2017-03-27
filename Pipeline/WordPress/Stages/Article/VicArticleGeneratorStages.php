<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Victoire\Bundle\BlogBundle\Entity\Article;
use Victoire\Bundle\BlogBundle\Entity\ArticleTemplate;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;

class VicArticleGeneratorStages implements StageInterface
{
    public function __invoke($playload)
    {
        foreach ($playload->getItems() as $plArticle) {
            $article = new Article();
            $article->setName($plArticle->getTitle());
            $playload->getNewBlog()->addArticle($article);
        }

        return $playload;
    }
}