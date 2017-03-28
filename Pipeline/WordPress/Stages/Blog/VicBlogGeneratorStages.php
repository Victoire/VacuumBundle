<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Blog;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStagesInterface;

/**
 * Class VicBlogGeneratorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog
 */
class VicBlogGeneratorStages implements PersisterStagesInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * VicBlogGeneratorStages constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $playload
     * @return mixed
     */
    public function __invoke($playload)
    {
        $blog = new Blog();
        $blog->setName($playload->getTitle());
        $blog->addWidgetMap(new WidgetMap());
        $blog->setPublishedAt($playload->getPublicationDate());
        $blog->setCreatedAt($playload->getPublicationDate());
        $playload->setNewBlog($blog);
        $this->entityManager->persist($blog);
        return $playload;
    }
}