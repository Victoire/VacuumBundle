<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Victoire\Bundle\BlogBundle\Entity\Article;
use Victoire\Bundle\BlogBundle\Entity\ArticleTemplate;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;

class VicArticleGeneratorStages implements StageInterface
{
    public function __invoke($playload)
    {
        $template = new ArticleTemplate();
        $template->setName("blog quovadis article template");
        $template->setBusinessEntityId("article");
        $template->setBackendName("quovadis-article-template");
        $template->setParent($playload->getRafinedData());

        foreach ($playload->getItems() as $plArticle) {
            $article = new Article();
            $article->setName($plArticle->title);
            $article->setTemplate($template);
            $playload->getNewBlog()->addArticle($article);
        }

        return $playload;
    }
}