<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress;

use Victoire\Bundle\BlogBundle\Entity\Blog;

/**
 * Class WordPressPlayload
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress
 */
class WordPressPlayload
{
    /**
     * @var mixed
     */
    private $rawData;

    /**
     * @var Blog
     */
    private $newBlog;

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
    private $language;

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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return WordPressPlayload
     */
    public function setTitle($title)
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
     * @return WordPressPlayload
     */
    public function setLink($link)
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
     * @return WordPressPlayload
     */
    public function setDescription($description)
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
     * @return WordPressPlayload
     */
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = $publicationDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return WordPressPlayload
     */
    public function setLanguage($language)
    {
        $this->language = $language;
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
     * @return WordPressPlayload
     */
    public function setBaseSiteUrl($baseSiteUrl)
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
     * @return WordPressPlayload
     */
    public function setBaseBlogUrl($baseBlogUrl)
    {
        $this->baseBlogUrl = $baseBlogUrl;
        return $this;
    }

    /**
     * @param $author
     * @return $this
     */
    public function addAuthor($author)
    {
        array_push($this->authors, $author);
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
     * @param $authorLogin
     * @return mixed
     */
    public function getAuthor($authorLogin)
    {
        foreach ($this->authors as $author) {
            if ($author->getAuthorLogin() == $authorLogin) {
                return $author;
            }
        }
    }

    /**
     * @param array $authors
     * @return WordPressPlayload
     */
    public function setAuthors(array $authors)
    {
        $this->authors = $authors;
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
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     * @return WordPressPlayload
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
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
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return WordPressPlayload
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @param $item
     * @return $this
     */
    public function addItem($item)
    {
        array_push($this->items, $item);
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
     * @return WordPressPlayload
     */
    public function setItems(array $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param mixed $rawData
     * @return WordPressPlayload
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
        return $this;
    }

    /**
     * @param $term
     * @return $this
     */
    public function addTerm($term)
    {
        array_push($this->terms, $term);
        return $this;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getTerm($id)
    {
        foreach ($this->getTerms() as $term) {
            if ($id == $term->getTermId()) {
                return $term;
            }
        }
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
     * @return WordPressPlayload
     */
    public function setTerms(array $terms)
    {
        $this->terms = $terms;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNewBlog()
    {
        return $this->newBlog;
    }

    /**
     * @param mixed $newBlog
     * @return WordPressPlayload
     */
    public function setNewBlog($newBlog)
    {
        $this->newBlog = $newBlog;
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
     * @return WordPressPlayload
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }
}