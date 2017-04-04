<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Tag;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;

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
     * @param $playload
     * @return mixed
     */
    public function __invoke(PlayloadInterface $playload)
    {
        $progress = $playload->getProgressBar(count($playload->getTags()));
        $playload->getOutput()->writeln(sprintf('Victoire Tag generation:'));

        foreach ($playload->getTags() as $wpTag) {
            $tag = new Tag();
            $tag->setTitle($wpTag->getTagName());
            $tag->setSlug(($wpTag->getTagSlug()));
            $playload->getNewBlog()->addTag($tag);

            $this->entityManager->persist($tag);
            $progress->advance();
        }

        $progress->finish();
        $playload->getSuccess();

        return $playload;
    }
}