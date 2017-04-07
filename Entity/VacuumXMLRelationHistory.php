<?php

namespace Victoire\DevTools\VacuumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class VacuumRelationHistoric.
 *
 * @ORM\Entity
 */
class VacuumXMLRelationHistory extends AbstractVacuumRelationHistory
{
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
     * @return string
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * @param string $tagName
     *
     * @return VacuumXMlRelationHistory
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
     *
     * @return VacuumXMlRelationHistory
     */
    public function setTagId(int $tagId)
    {
        $this->tagId = $tagId;

        return $this;
    }
}
