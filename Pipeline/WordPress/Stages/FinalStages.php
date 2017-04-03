<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages;

use Doctrine\ORM\EntityManager;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;

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
    public function __invoke(PlayloadInterface $playload)
    {
        $playload->getOutput()->write('Flush blog:');

        $this->entityManager->persist($playload->getNewBlog());
        $this->entityManager->flush();

        $playload->getOutput()->write(' success');
        return $playload;
    }
}