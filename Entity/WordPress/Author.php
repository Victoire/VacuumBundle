<?php

namespace Victoire\DevTools\VacuumBundle\Entity\WordPress;

class Author
{
    private $wpAuthorId;

    private $authorLogin;

    private $authorEmail;

    private $authorDisplayName;

    private $authorFirstName;

    private $authorLastName;

    /**
     * @return mixed
     */
    public function getWpAuthorId()
    {
        return $this->wpAuthorId;
    }

    /**
     * @param mixed $wpAuthorId
     * @return Author
     */
    public function setWpAuthorId($wpAuthorId)
    {
        $this->wpAuthorId = $wpAuthorId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorLogin()
    {
        return $this->authorLogin;
    }

    /**
     * @param mixed $authorLogin
     * @return Author
     */
    public function setAuthorLogin($authorLogin)
    {
        $this->authorLogin = $authorLogin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * @param mixed $authorEmail
     * @return Author
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorDisplayName()
    {
        return $this->authorDisplayName;
    }

    /**
     * @param mixed $authorDisplayName
     * @return Author
     */
    public function setAuthorDisplayName($authorDisplayName)
    {
        $this->authorDisplayName = $authorDisplayName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorFirstName()
    {
        return $this->authorFirstName;
    }

    /**
     * @param mixed $authorFirstName
     * @return Author
     */
    public function setAuthorFirstName($authorFirstName)
    {
        $this->authorFirstName = $authorFirstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorLastName()
    {
        return $this->authorLastName;
    }

    /**
     * @param mixed $authorLastName
     * @return Author
     */
    public function setAuthorLastName($authorLastName)
    {
        $this->authorLastName = $authorLastName;
        return $this;
    }
}