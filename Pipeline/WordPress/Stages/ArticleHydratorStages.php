<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages;

use Victoire\DevTools\VacuumBundle\Entity\Playload;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;

class ArticleHydratorStages implements StageInterface
{
    public function __invoke(Playload $playload)
    {
        foreach ($playload->getResult() as $result) {
            $rawData = $result->getRawData();
            $article = $result->getArticle();
            if (!empty($rawData->title)) {
                $article->setName((string) $rawData->title);
            }
            if (!empty($rawData->description)) {
                $article->setDescription((string) $rawData->description);
            }
            if (!empty($rawData->pubDate)) {
                $date = new \DateTime((string) $rawData->pubDate);

            }
            $result->setArticle($article);
        }

        return $playload;
    }
}