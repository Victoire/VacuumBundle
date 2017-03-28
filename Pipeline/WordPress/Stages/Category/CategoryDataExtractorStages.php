<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Category;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class CategoryDataExtractorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category
 */
class CategoryDataExtractorStages implements StageInterface
{
    /**
     * @param $playload
     * @return mixed
     */
    public function __invoke($playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        foreach ($playload->getRawData()->channel as $blog) {
            foreach ($blog->category as $wpCategory) {
                $category = new Category();
                $category->setTerm($playload->getTerm($xmlDataFormater->formatInteger('term_id', $wpCategory)));
                $category->setCategoryName($xmlDataFormater->formatString('cat_name', $wpCategory));
                $category->setCategoryNicename($xmlDataFormater->formatString('category_nicename', $wpCategory));
                $category->setCategoryParent($xmlDataFormater->formatInteger('category_parent', $wpCategory));

                $playload->addCategory($category);
            }
        }

        unset($xmlDataFormater);
        return $playload;
    }
}