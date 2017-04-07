<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages;

use Doctrine\ORM\EntityManager;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;

/**
 * Class FinalStages.
 */
class FinalStages implements PersisterStageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * FinalStages constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $payload
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $payload->getNewStageTitleMessage('Flush blog:');

        $this->entityManager->persist($payload->getNewVicBlog());
        $this->entityManager->flush();

        $payload->getNewSuccessMessage(' success');
        $payload->jumpLine();

        return $payload;
    }
}
