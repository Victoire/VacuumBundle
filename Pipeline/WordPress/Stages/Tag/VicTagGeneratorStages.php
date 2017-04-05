<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Tag;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;

/**
 * Class VicTagGeneratorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag
 */
class VicTagGeneratorStages implements PersisterStageInterface
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
     * Transfer tag form tmpBlog to Victoire blog.
     *
     * @param $playload
     * @return mixed
     */
    public function __invoke(CommandPlayloadInterface $playload)
    {
        $progress = $playload->getNewProgressBar(count($playload->getTmpBlog()->getTags()));
        $playload->getNewStageTitleMessage("Victoire Tag generation:");

        foreach ($playload->getTmpBlog()->getTags() as $wpTag) {
            $tag = new Tag();
            $tag->setTitle($wpTag->getTagName());
            $tag->setSlug(($wpTag->getTagSlug()));
            $playload->getNewVicBlog()->addTag($tag);

            $this->entityManager->persist($tag);
            $progress->advance();
        }

        $progress->finish();
        $playload->getNewSuccessMessage(" success");
        $playload->jumpLine();

        return $playload;
    }
}