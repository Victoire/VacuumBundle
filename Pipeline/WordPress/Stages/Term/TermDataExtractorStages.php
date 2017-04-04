<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Term;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Term;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class TermDataExtractorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Term
 */
class TermDataExtractorStages implements StageInterface
{
    /**
     * @param $playload
     * @return mixed
     */
    public function __invoke(PlayloadInterface $playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        foreach ($playload->getRawData()->channel as $blog) {
            $progress = $playload->getProgressBar(count($blog->term));
            $playload->getOutput()->writeln(sprintf('Term data extraction:'));
            foreach ($blog->term as $wpTerm) {
                $term = new Term();
                $term->setTermId($xmlDataFormater->formatInteger('term_id', $wpTerm));
                $term->setTermTaxonomy($xmlDataFormater->formatString('term_taxonomy', $wpTerm));
                $term->setTermSlug($xmlDataFormater->formatString('term_slug', $wpTerm));
                $term->setParent($xmlDataFormater->formatInteger('term_parent', $wpTerm));
                $playload->addTerm($term);
                $progress->advance();
            }
        }
        $progress->finish();
        $playload->getSuccess();

        unset($xmlDataFormater);
        return $playload;
    }
}