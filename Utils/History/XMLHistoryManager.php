<?php

namespace Victoire\DevTools\VacuumBundle\Utils\History;

use Doctrine\ORM\EntityManager;
use Victoire\DevTools\VacuumBundle\Entity\AbstractVacuumRelationHistory;
use Victoire\DevTools\VacuumBundle\Entity\VacuumXMlRelationHistory;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\AbstractXMLEntity;
use Victoire\DevTools\VacuumBundle\Utils\History\Exception\XMLHistoryException;

class XMLHistoryManager implements HistoryManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var array|\Victoire\DevTools\VacuumBundle\Entity\VacuumXMlRelationHistory[]
     */
    private $histories;

    /**
     * Reader constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * Reload Histories.
     */
    public function reload()
    {
        unset($this->histories);
        $this->histories = $this->entityManager
            ->getRepository('VictoireVacuumBundle:VacuumXMlRelationHistory')
            ->findAll();
    }

    /**
     * @param AbstractXMLEntity $source
     * @param $vicClass
     *
     * @return mixed|null|VacuumXMlRelationHistory
     */
    public function searchHistory(AbstractXMLEntity $source, $vicClass)
    {
        foreach ($this->histories as $history) {
            if ($history->getTagName() == $source->getXmlTag() &&
                $history->getTagId() == $source->getId() &&
                $history->getVicClass() == $vicClass
            ) {
                return $history;
            }
        }
    }

    /**
     * @param AbstractVacuumRelationHistory $history
     *
     * @throws XMLHistoryException
     *
     * @return null|object
     */
    public function getVicEntity(AbstractVacuumRelationHistory $history)
    {
        $repo = $this->entityManager->getRepository($history->getVicClass());
        $entity = $repo->find($history->getVicId());

        if (null == $entity) {
            $message = sprintf('Can\'t found history linked entity with following parameters ID: "%s", ClassName: "%s"',
                $history->getVicId(),
                $history->getVicClass()
            );
            throw new XMLHistoryException($message);
        }

        return $entity;
    }

    /**
     * @param AbstractXMLEntity $source
     * @param $refined
     *
     * @return VacuumXMlRelationHistory
     */
    public function generateHistory(AbstractXMLEntity $source, $refined)
    {
        $history = new VacuumXMlRelationHistory();
        $history->setTagId($source->getId());
        $history->setTagName($source->getXmlTag());
        $history->setVicClass(get_class($refined));
        if (null != $refined->getId()) {
            $history->setVicId($refined->getId());
        }

        return $history;
    }

    /**
     * @param $entity
     * @param VacuumXMlRelationHistory $history
     */
    public function flushHistory($entity, VacuumXMlRelationHistory $history)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $history->setVicId($entity->getId());
        $this->entityManager->persist($history);
        $this->entityManager->flush();
    }
}
