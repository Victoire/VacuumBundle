<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog;

use Victoire\Bundle\BlogBundle\Entity\Blog;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;

class VicBlogGeneratorStages implements StageInterface
{
    public function __invoke($playload)
    {
        $blog = new Blog();
        $blog->setName($playload->getTitle());
        $blog->addWidgetMap(new WidgetMap());
        $blog->setPublishedAt($playload->getPublicationDate());
        $blog->setCreatedAt($playload->getPublicationDate());
        $playload->setNewBlog($blog);
    }
}