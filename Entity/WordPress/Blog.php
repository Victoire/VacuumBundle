<?php

namespace Victoire\DevTools\VacuumBundle\Entity\WordPress;

use Victoire\Bundle\MediaBundle\Entity\Folder;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;

/**
 * Class Blog
 * @package Victoire\DevTools\VacuumBundle\Entity\WordPress
 */
class Blog extends AbstractXMLEntity
{
    /**
     * @var Folder
     */
    private $blogFolder;

    /**
     * @var WidgetMap
     */
    private $contentWidgetMap;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $link;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $publicationDate;

    /**
     * @var string
     */
    private $baseSiteUrl;

    /**
     * @var string
     */
    private $baseBlogUrl;

    /**
     * @var array
     */
    private $authors = [];

    /**
     * @var array
     */
    private $categories = [];

    /**
     * @var array
     */
    private $tags = [];

    /**
     * @var array
     */
    private $items = [];

    /**
     * @var array
     */
    private $terms = [];

    /**
     * @var array
     */
    private $seos = [];

    /**
     * @var array
     */
    private $articles = [];

    /**
     * @return Folder
     */
    public function getBlogFolder()
    {
        return $this->blogFolder;
    }

    /**
     * @param Folder $blogFolder
     * @return Blog
     */
    public function setBlogFolder(Folder $blogFolder)
    {
        $this->blogFolder = $blogFolder;
        return $this;
    }

    /**
     * @return WidgetMap
     */
    public function getContentWidgetMap()
    {
        return $this->contentWidgetMap;
    }

    /**
     * @param WidgetMap $contentWidgetMap
     * @return Blog
     */
    public function setContentWidgetMap(WidgetMap $contentWidgetMap)
    {
        $this->contentWidgetMap = $contentWidgetMap;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return Blog
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Blog
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return Blog
     */
    public function setLink(string $link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Blog
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * @param \DateTime $publicationDate
     * @return Blog
     */
    public function setPublicationDate(\DateTime $publicationDate)
    {
        $this->publicationDate = $publicationDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseSiteUrl()
    {
        return $this->baseSiteUrl;
    }

    /**
     * @param string $baseSiteUrl
     * @return Blog
     */
    public function setBaseSiteUrl(string $baseSiteUrl)
    {
        $this->baseSiteUrl = $baseSiteUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseBlogUrl()
    {
        return $this->baseBlogUrl;
    }

    /**
     * @param string $baseBlogUrl
     * @return Blog
     */
    public function setBaseBlogUrl(string $baseBlogUrl)
    {
        $this->baseBlogUrl = $baseBlogUrl;
        return $this;
    }

    /**
     * @return array
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param $login
     * @return mixed
     */
    public function getAuthor($username)
    {
        foreach ($this->authors as $author) {
            if ($author->getUsername() == $username) {
                return $author;
            }
        }
    }

    /**
     * @param array $authors
     * @return Blog
     */
    public function setAuthors(array $authors)
    {
        $this->authors = $authors;
        return $this;
    }

    /**
     * @param $author
     * @return $this
     */
    public function addAuthors($author)
    {
        array_push($this->authors, $author);
        return $this;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     * @return Blog
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @param $category
     * @return $this
     */
    public function addCategory($category)
    {
        array_push($this->categories, $category);
        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return Blog
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @param $tag
     * @return $this
     */
    public function addTag($tag)
    {
        array_push($this->tags, $tag);
        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return Blog
     */
    public function setItems(array $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return array
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * @param array $terms
     * @return Blog
     */
    public function setTerms(array $terms)
    {
        $this->terms = $terms;
        return $this;
    }

    /**
     * @return array
     */
    public function getSeos()
    {
        return $this->seos;
    }

    /**
     * @param array $seos
     * @return Blog
     */
    public function setSeos(array $seos)
    {
        $this->seos = $seos;
        return $this;
    }

    /**
     * @return array
     */
    public function getArticles(): array
    {
        return $this->articles;
    }

    /**
     * @param array $articles
     * @return Blog
     */
    public function setArticles(array $articles): Blog
    {
        $this->articles = $articles;
        return $this;
    }

    /**
     * @param $article
     * @return $this
     */
    public function addArticle($article)
    {
        array_push($this->articles, $article);
        return $this;
    }
}