<?php

namespace Victoire\DevTools\VacuumBundle\Entity;

/**
 * Class Playload
 * @package Victoire\DevTools\VacuumBundle\Entity
 */
class Playload
{
    /**
     * @var array
     */
    protected $result = [];

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
        array_push($this->result, $result);
        return $this;
    }
}