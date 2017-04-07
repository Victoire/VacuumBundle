<?php

namespace Victoire\DevTools\VacuumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class VacuumRelationHistoric
 * @package Victoire\DevTools\VacuumBundle\Entity
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *      "xml" = "Victoire\DevTools\VacuumBundle\Entity\VacuumXMlRelationHistory"
 * })
 * @ORM\Table("vic_vacuum_historic")
 */
abstract class AbstractVacuumRelationHistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="vic_class", type="string", length=255)
     */
    private $vicClass;

    /**
     * @var int
     *
     * @ORM\Column(name="vic_id", type="integer", nullable=true)
     */
    private $vicId;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return AbstractVacuumRelationHistory
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getVicClass()
    {
        return $this->vicClass;
    }

    /**
     * @param string $vicClass
     * @return AbstractVacuumRelationHistory
     */
    public function setVicClass(string $vicClass)
    {
        $this->vicClass = $vicClass;
        return $this;
    }

    /**
     * @return int
     */
    public function getVicId()
    {
        return $this->vicId;
    }

    /**
     * @param int $vicId
     * @return AbstractVacuumRelationHistory
     */
    public function setVicId(int $vicId)
    {
        $this->vicId = $vicId;
        return $this;
    }
}