<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag;

use Victoire\Bundle\BlogBundle\Entity\Tag;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;

class TagGeneratorStages implements StageInterface
{
    public function __invoke($playload)
    {
        foreach ($playload->getTags() as $wpTag) {
            $tag = new Tag();
            $tag->setTitle($wpTag->getTitle());
            $tag->setSlug(($wpTag->getSlug()));
            $playload->getNewBlog()->addTag($tag);
        }

        return $playload;
    }
}