<?php

namespace Victoire\DevTools\VacuumBundle\Utils\History;

use Doctrine\ORM\EntityManager;
use Victoire\DevTools\VacuumBundle\Entity\AbstractVacuumRelationHistory;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\AbstractXMLEntity;
use Victoire\DevTools\VacuumBundle\Utils\History\Exception\XMLHistoryException;

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
     * Search History entry by xmlTag and dump id
     * return null if nothing found or
     * return VacuumXMLRelationHistory Entity
     *
     * @param AbstractXMLEntity $source
     * @return mixed
     */
    public function searchHistory(AbstractXMLEntity $source, $vicClass);

    /**
     * @param AbstractVacuumRelationHistory $history
     * @return null|object
     * @throws XMLHistoryException
     */
    public function getVicEntity(AbstractVacuumRelationHistory $history);
}