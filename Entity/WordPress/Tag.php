<?php

namespace Victoire\DevTools\VacuumBundle\Entity\WordPress;

class Tag extends AbstractXMLEntity
{
    /**
     * @var Term
     */
    private $term;

    /**
     * @var string
     */
    private $tagSlug;

    /**
     * @var string
     */
    private $tagName;

    /**
     * @return mixed
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @param mixed $term
     * @return Tag
     */
    public function setTerm($term)
    {
        $this->term = $term;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTagSlug()
    {
        return $this->tagSlug;
    }

    /**
     * @param mixed $tagSlug
     * @return Tag
     */
    public function setTagSlug($tagSlug)
    {
        $this->tagSlug = $tagSlug;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * @param mixed $tagName
     * @return Tag
     */
    public function setTagName($tagName)
    {
        $this->tagName = $tagName;
        return $this;
    }
}