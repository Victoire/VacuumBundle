<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Utils\Faker;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Tag;

/**
 * Class TagFaker.
 */
class TagFaker
{
    /**
     * Generate x WordPress Tag in a WordPress Blog.
     *
     * @param $nb
     * @param $tmpBlog
     */
    public function generateWPTag($nb, $tmpBlog)
    {
        for ($ii = 1; $ii < $nb + 1; $ii++) {
            $tag = new Tag();
            $tag->setTagName('Test '.$ii);
            $tag->setTagSlug('test-tag-'.$ii);
            $tag->setXmlTag('tag');
            $tag->setId($ii);
            $tmpBlog->addTag($tag);
        }
    }

    /**
     * Generate x Victoire Tag in a Victoire Blog.
     *
     * @param $nb
     * @param $blog
     */
    public function generateVicTag($nb, $blog)
    {
        for ($ii = 1; $ii < $nb + 1; $ii++) {
            $tag = new \Victoire\Bundle\BlogBundle\Entity\Tag();
            $tag->setTitle('Test '.$ii);
            $tag->setSlug('test-tag-'.$ii);
            $blog->addTag($tag);
        }
    }
}
