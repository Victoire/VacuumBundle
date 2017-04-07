<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Category;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;
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
     * @param $payload
     * @return mixed
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $xmlDataFormater = new XmlDataFormater();

        $channel = $payload->getRawData()->channel;

        $progress = $payload->getNewProgressBar(count($channel->category));
        $payload->getNewStageTitleMessage("Category data extraction:");

        foreach ($channel->category as $wpCategory) {
            $category = new Category();
            $category->setId($xmlDataFormater->formatInteger('term_id', $wpCategory));
            $category->setXmlTag("category");
            $category->setCategoryName($xmlDataFormater->formatString('cat_name', $wpCategory));
            $category->setCategoryNicename($xmlDataFormater->formatString('category_nicename', $wpCategory));
            $category->setCategoryParent($xmlDataFormater->formatInteger('category_parent', $wpCategory));

            $payload->getTmpBlog()->addCategory($category);
            $progress->advance();
        }
        $progress->finish();

        $payload->getNewSuccessMessage(" success");
        $payload->jumpLine();

        unset($xmlDataFormater);
        return $payload;
    }
}