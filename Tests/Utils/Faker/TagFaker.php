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
     * @param $nb
     * @param null $blog
     * @return array
     */
    public function generateVicTag($nb, $blog = null)
    {
        $tags = [];

        for ($ii = 1; $ii < $nb + 1; $ii++) {
            $tag = new \Victoire\Bundle\BlogBundle\Entity\Tag();
            $tag->setTitle('Test '.$ii);
            $tag->setSlug('test-tag-'.$ii);
            if (null != $blog) {
                $blog->addTag($tag);
            } else {
                array_push($tags, $tag);
            }
        }

        if (null == $blog) {
            return $tags;
        }
    }
}
