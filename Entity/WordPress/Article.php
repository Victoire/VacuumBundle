<?php

namespace Victoire\DevTools\VacuumBundle\Entity\WordPress;


class Article
{
    private $title;

    private $link;

    private $pubDate;

    private $creator;

    private $description;

    private $content;

    private $excerpt;

    private $postId;

    private $postDate;

    private $postDateGmt;

    private $status;

    private $postParent;

    private $menuOrder;

    private $postType;

    private $attachmentUrl;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     * @return Article
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }

    /**
     * @param mixed $pubDate
     * @return Article
     */
    public function setPubDate($pubDate)
    {
        $this->pubDate = $pubDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param mixed $creator
     * @return Article
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return Article
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExcerpt()
    {
        return $this->excerpt;
    }

    /**
     * @param mixed $excerpt
     * @return Article
     */
    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param mixed $postId
     * @return Article
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    /**
     * @param mixed $postDate
     * @return Article
     */
    public function setPostDate($postDate)
    {
        $this->postDate = $postDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostDateGmt()
    {
        return $this->postDateGmt;
    }

    /**
     * @param mixed $postDateGmt
     * @return Article
     */
    public function setPostDateGmt($postDateGmt)
    {
        $this->postDateGmt = $postDateGmt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return Article
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostParent()
    {
        return $this->postParent;
    }

    /**
     * @param mixed $postParent
     * @return Article
     */
    public function setPostParent($postParent)
    {
        $this->postParent = $postParent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMenuOrder()
    {
        return $this->menuOrder;
    }

    /**
     * @param mixed $menuOrder
     * @return Article
     */
    public function setMenuOrder($menuOrder)
    {
        $this->menuOrder = $menuOrder;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostType()
    {
        return $this->postType;
    }

    /**
     * @param mixed $postType
     * @return Article
     */
    public function setPostType($postType)
    {
        $this->postType = $postType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttachmentUrl()
    {
        return $this->attachmentUrl;
    }

    /**
     * @param mixed $attachmentUrl
     * @return Article
     */
    public function setAttachmentUrl($attachmentUrl)
    {
        $this->attachmentUrl = $attachmentUrl;
        return $this;
    }
}