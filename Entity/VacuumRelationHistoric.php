<?php

namespace Victoire\DevTools\VacuumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class VacuumRelationHistoric
 * @package Victoire\DevTools\VacuumBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table("vic_vacuum_historic")
 */
class VacuumRelationHistoric
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
     * @ORM\Column(name="vic_id", type="integer")
     */
    private $vicId;

    /**
     * @var string
     *
     * @ORM\Column(name="tag_name", type="string", length=255)
     */
    private $tagName;

    /**
     * @var int
     *
     * @ORM\Column(name="tag_id", type="integer")
     */
    private $tagId;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return VacuumRelationHistoric
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
     * @return VacuumRelationHistoric
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
     * @return VacuumRelationHistoric
     */
    public function setVicId(int $vicId)
    {
        $this->vicId = $vicId;
        return $this;
    }

    /**
     * @return string
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * @param string $tagName
     * @return VacuumRelationHistoric
     */
    public function setTagName(string $tagName)
    {
        $this->tagName = $tagName;
        return $this;
    }

    /**
     * @return int
     */
    public function getTagId()
    {
        return $this->tagId;
    }

    /**
     * @param int $tagId
     * @return VacuumRelationHistoric
     */
    public function setTagId(int $tagId)
    {
        $this->tagId = $tagId;
        return $this;
    }
}