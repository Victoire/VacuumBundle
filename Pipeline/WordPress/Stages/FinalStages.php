<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages;

use Doctrine\ORM\EntityManager;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;

/**
 * Class FinalStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages
 */
class FinalStages implements PersisterStageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * FinalStages constructor.
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
     */
    public function __invoke(CommandPlayloadInterface $playload)
    {
        $playload->getNewStageTitleMessage('Flush blog:');

        $this->entityManager->persist($playload->getNewVicBlog());
        $this->entityManager->flush();

        $playload->getNewSuccessMessage(" success");
        $playload->jumpLine();
        return $playload;
    }
}