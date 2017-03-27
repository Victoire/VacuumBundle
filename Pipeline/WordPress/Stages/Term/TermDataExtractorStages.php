<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Term;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Term;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

class TermDataExtractorStages implements StageInterface
{
    public function __invoke($playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        foreach ($playload->getRawData()->channel as $blog) {
            foreach ($blog->term as $wpTerm) {
                $term = new Term();
                $term->setTermId($xmlDataFormater->formatInteger('term_id', $wpTerm));
                $term->setTermTaxonomy($xmlDataFormater->formatString('term_taxonomy', $wpTerm));
                $term->setTermSlug($xmlDataFormater->formatString('term_slug', $wpTerm));
                $term->setParent($xmlDataFormater->formatInteger('term_parent', $wpTerm));
                $playload->addTerm($term);
            }
        }

        unset($xmlDataFormater);
        return $playload;
    }
}