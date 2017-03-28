<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Locale;

use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

class LocaleStages implements StageInterface
{
    public function __invoke($playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        foreach ($playload->getRawData()->channel as $blog) {
            $locale = $xmlDataFormater->formatString("language", $blog);
            $locale = explode("-", $locale);
            $playload->setLocale($locale[0]);
        }

        return $playload;
    }
}