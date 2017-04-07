<?php

namespace Victoire\DevTools\VacuumBundle\Entity\WordPress;

/**
 * Class XMLEntity.
 */
abstract class AbstractXMLEntity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $xmlTag;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return AbstractXMLEntity
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getXmlTag()
    {
        return $this->xmlTag;
    }

    /**
     * @param string $xmlTag
     *
     * @return AbstractXMLEntity
     */
    public function setXmlTag(string $xmlTag)
    {
        $this->xmlTag = $xmlTag;

        return $this;
    }
}
