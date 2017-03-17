<?php

namespace Victoire\DevTools\VacuumBundle\Utils\Xml;

/**
 * Class XmlDataFormater
 * @package Victoire\DevTools\VacuumBundle\Utils\Xml
 */
class XmlDataFormater
{
    /**
     * @param $node
     * @param $simpleXml
     * @return null|string
     */
    public function formatString($node, $simpleXml)
    {
        if (!empty($simpleXml->$node)) {
            return (string) $simpleXml->$node;
        }

        return null;
    }

    /**
     * @param $node
     * @param $simpleXml
     * @return \DateTime|null|string
     */
    public function formatDate($node, $simpleXml)
    {
        $date = $this->formatString($node, $simpleXml);
        if (null != $date) {
            $date = new \DateTime($date);
            return $date;
        }

        return null;
    }

    /**
     * @param $node
     * @param $simpleXml
     * @return int|null
     */
    public function formatInteger($node, $simpleXml)
    {
        if (!empty($simpleXml->$node)) {
            return (integer) $simpleXml->$node;
        }

        return null;
    }
}