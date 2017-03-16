<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress;

class WordPressPlayload
{
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
    private $authors;

    /**
     * @var array
     */
    private $categories;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var array
     */
    private $items;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return WordPressPlayload
     */
    public function setTitle(string $title): WordPressPlayload
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return WordPressPlayload
     */
    public function setLink(string $link): WordPressPlayload
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return WordPressPlayload
     */
    public function setDescription(string $description): WordPressPlayload
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublicationDate(): \DateTime
    {
        return $this->publicationDate;
    }

    /**
     * @param \DateTime $publicationDate
     * @return WordPressPlayload
     */
    public function setPublicationDate(\DateTime $publicationDate): WordPressPlayload
    {
        $this->publicationDate = $publicationDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return WordPressPlayload
     */
    public function setLanguage(string $language): WordPressPlayload
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseSiteUrl(): string
    {
        return $this->baseSiteUrl;
    }

    /**
     * @param string $baseSiteUrl
     * @return WordPressPlayload
     */
    public function setBaseSiteUrl(string $baseSiteUrl): WordPressPlayload
    {
        $this->baseSiteUrl = $baseSiteUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseBlogUrl(): string
    {
        return $this->baseBlogUrl;
    }

    /**
     * @param string $baseBlogUrl
     * @return WordPressPlayload
     */
    public function setBaseBlogUrl(string $baseBlogUrl): WordPressPlayload
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
    public function getAuthors(): array
    {
        return $this->authors;
    }

    /**
     * @param array $authors
     * @return WordPressPlayload
     */
    public function setAuthors(array $authors): WordPressPlayload
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
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     * @return WordPressPlayload
     */
    public function setCategories(array $categories): WordPressPlayload
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
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return WordPressPlayload
     */
    public function setTags(array $tags): WordPressPlayload
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
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return WordPressPlayload
     */
    public function setItems(array $items): WordPressPlayload
    {
        $this->items = $items;
        return $this;
    }
}