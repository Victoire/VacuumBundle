<?php

namespace Victoire\DevTools\VacuumBundle\Entity\WordPress;

class Category extends AbstractXMLEntity
{
    /**
     * @var Term
     */
    private $term;

    /**
     * @var string
     */
    private $categoryNicename;

    /**
     * @var string
     */
    private $categoryParent;

    /**
     * @var string
     */
    private $categoryName;

    /**
     * @return Term
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @param Term $term
     * @return Category
     */
    public function setTerm($term)
    {
        $this->term = $term;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategoryNicename()
    {
        return $this->categoryNicename;
    }

    /**
     * @param mixed $categoryNicename
     * @return Category
     */
    public function setCategoryNicename($categoryNicename)
    {
        $this->categoryNicename = $categoryNicename;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategoryParent()
    {
        return $this->categoryParent;
    }

    /**
     * @param mixed $categoryParent
     * @return Category
     */
    public function setCategoryParent($categoryParent)
    {
        $this->categoryParent = $categoryParent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * @param mixed $categoryName
     * @return Category
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
        return $this;
    }
}