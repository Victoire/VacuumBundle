<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Category;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class CategoryDataExtractorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category
 */
class CategoryDataExtractorStages implements StageInterface
{
    /**
     * Extract and add category from raw Data to tmpBlog
     *
     * @param $playload
     * @return mixed
     */
    public function __invoke(CommandPlayloadInterface $playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        $channel = $playload->getRawData()->channel;

        $progress = $playload->getNewProgressBar(count($channel->category));
        $playload->getNewStageTitleMessage("Category data extraction:");

        foreach ($channel->category as $wpCategory) {
            $category = new Category();
            $category->setCategoryName($xmlDataFormater->formatString('cat_name', $wpCategory));
            $category->setCategoryNicename($xmlDataFormater->formatString('category_nicename', $wpCategory));
            $category->setCategoryParent($xmlDataFormater->formatInteger('category_parent', $wpCategory));

            $playload->getTmpBlog()->addCategory($category);
            $progress->advance();
        }
        $progress->finish();

        $playload->getNewSuccessMessage(" success");
        $playload->jumpLine();

        unset($xmlDataFormater);
        return $playload;
    }
}