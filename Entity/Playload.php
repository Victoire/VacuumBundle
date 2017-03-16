<?php

namespace Victoire\DevTools\VacuumBundle\Entity;

/**
 * Class Playload
 * @package Victoire\DevTools\VacuumBundle\Entity
 */
class Playload
{
    /**
     * @var null|string
     */
    protected $result = null;
    /**
     * @return null
     */
    public function getResult()
    {
        return $this->result;
    }
    /**
     * @param string $result
     * @return static
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }
    /**
     * @param $result
     * @return $this
     */
    public function addResult($result)
    {
        $this->result .= $result;
        return $this;
    }
}