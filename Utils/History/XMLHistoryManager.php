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
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
        $this->histories = $this->entityManager
            ->getRepository('VictoireVacuumBundle:VacuumXMlRelationHistory')
            ->findAll()
        ;
    }

    /**
     * Reload Histories
     */
    public function reload()
    {
        unset($this->histories);
        $this->histories = $this->entityManager
            ->getRepository('VictoireVacuumBundle:VacuumXMlRelationHistory')
            ->findAll()
        ;
    }

    /**
     * @param AbstractXMLEntity $source
     * @param $refined
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

        if (!$this->isReferenceExist($history)) {
            $this->entityManager->persist($history);
        }

        return $history;
    }

    /**
     * @param AbstractVacuumRelationHistory $history
     * @return bool
     * @throws XMLHistoryException
     */
    public function isReferenceExist(AbstractVacuumRelationHistory $history)
    {
        if (get_class($history) == VacuumXMlRelationHistory::class) {
            $refClass = new \ReflectionClass($history);
            foreach ($this->histories as $currentHistory) {
                if ($currentHistory == $history) {
                    return true;
                } else {
                    $matchingArg = 0;
                    foreach ($refClass->getMethods() as $method) {
                        $testedHistoryMethodResult = call_user_func([get_class($history), $method->getName()]);
                        $currentHistoryMethodResult = call_user_func([VacuumXMlRelationHistory::class, $method->getName()]);
                        if ( $testedHistoryMethodResult == $currentHistoryMethodResult ) {
                            $matchingArg++;
                        }
                    }
                    if ($matchingArg >= 3) {
                        return true;
                    }
                }
            }
        } else {
            $message = sprintf('Wrong History class given. Should be "%s" instead of "%s"',
                VacuumXMlRelationHistory::class,
                get_class($history)
            );
            throw new XMLHistoryException($message);
        }

        return false;
    }
}