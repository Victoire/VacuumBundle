<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Tag;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStagesInterface;

/**
 * Class VicTagGeneratorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag
 */
class VicTagGeneratorStages implements PersisterStagesInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * TagGeneratorStages constructor.
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
        foreach ($playload->getTags() as $wpTag) {
            $tag = new Tag();
            $tag->setTitle($wpTag->getTagName());
            $tag->setSlug(($wpTag->getTagSlug()));
            $playload->getNewBlog()->addTag($tag);

            $this->entityManager->persist($tag);
        }

        return $playload;
    }
}