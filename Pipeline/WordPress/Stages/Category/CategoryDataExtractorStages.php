<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Category;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
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
    public function __invoke(PlayloadInterface $playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        foreach ($playload->getRawData()->channel as $blog) {
            $progress = $playload->getProgressBar(count($blog->category));
            $playload->getOutput()->writeln(sprintf('Category data extraction:'));
            foreach ($blog->category as $wpCategory) {
                $category = new Category();
                $category->setTerm($playload->getTerm($xmlDataFormater->formatInteger('term_id', $wpCategory)));
                $category->setCategoryName($xmlDataFormater->formatString('cat_name', $wpCategory));
                $category->setCategoryNicename($xmlDataFormater->formatString('category_nicename', $wpCategory));
                $category->setCategoryParent($xmlDataFormater->formatInteger('category_parent', $wpCategory));

                $playload->addCategory($category);
                $progress->advance();
            }
        }
        $playload->getOutput()->writeln(' success');

        unset($xmlDataFormater);
        return $playload;
    }
}