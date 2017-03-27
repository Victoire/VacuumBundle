<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category;

use Victoire\Bundle\BlogBundle\Entity\Category;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;

class CategoryGeneratorStages implements StageInterface
{
    public function __invoke($playload)
    {
        foreach ($playload->getCategories() as $plCategory) {
            $category = new Category();
            $category->setTitle($plCategory->getCategoryName());
            $category->setSlug($plCategory->getCategoryNiceName());
            $playload->getNewBlog()->addCategorie($category);
        }

        return $playload;
    }
}