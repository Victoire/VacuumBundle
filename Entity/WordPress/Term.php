<?php
/**
 * Created by PhpStorm.
 * User: made
 * Date: 16/03/17
 * Time: 17:21
 */

namespace Victoire\DevTools\VacuumBundle\Entity\WordPress;


class Term
{
    private $termId;

    private $termTaxonomy;

    private $termSlug;

    private $parent;

    /**
     * @return mixed
     */
    public function getTermId()
    {
        return $this->termId;
    }

    /**
     * @param mixed $termId
     * @return Term
     */
    public function setTermId($termId)
    {
        $this->termId = $termId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTermTaxonomy()
    {
        return $this->termTaxonomy;
    }

    /**
     * @param mixed $termTaxonomy
     * @return Term
     */
    public function setTermTaxonomy($termTaxonomy)
    {
        $this->termTaxonomy = $termTaxonomy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTermSlug()
    {
        return $this->termSlug;
    }

    /**
     * @param mixed $termSlug
     * @return Term
     */
    public function setTermSlug($termSlug)
    {
        $this->termSlug = $termSlug;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     * @return Term
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }
}