<?php

namespace Victoire\DevTools\VacuumBundle\Utils\History;

use Doctrine\ORM\EntityManager;
use Victoire\DevTools\VacuumBundle\Entity\AbstractVacuumRelationHistory;

/**
 * Class ReaderInterface
 * @package Victoire\DevTools\VacuumBundle\Utils\History
 */
interface HistoryManagerInterface
{
    /**
     * ReaderInterface constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager);

    /**
     * Only this function should be used to reload History
     */
    public function reload();

    /**
     * Will search for history.
     * return true when a match is made
     * false if not.
     *
     * @param AbstractVacuumRelationHistory $history
     * @return boolean
     */
    public function isReferenceExist(AbstractVacuumRelationHistory $history);
}