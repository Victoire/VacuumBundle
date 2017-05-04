<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Utils\Faker;

use Victoire\Bundle\BlogBundle\Entity\Blog;
use Victoire\Bundle\I18nBundle\Entity\ViewTranslation;
use Victoire\Bundle\PageBundle\Entity\Page;
use Victoire\Bundle\TemplateBundle\Entity\Template;

/**
 * Class BlogFaker.
 */
class BlogFaker
{
    /**
     * @param null $template
     * @param null $page
     *
     * @return Blog
     */
    public function getNewVicBlog($template = null, $page = null)
    {
        $blog = new Blog();
        $blog->setCurrentLocale('en');
        $blog->setDefaultLocale('en');
        $blog->setStatus('published');
        $blog->setPublishedAt(new \DateTime('Tue, 02 May 2017 13:56:23 +0000'));
        $translation = new ViewTranslation();
        $translation->setLocale('en');
        $translation->setName('blog test');
        $translation->setTranslatable($blog);
        $blog->addTranslation($translation);
        $blog->setTemplate(null == $template ? new Template() : $template);
        $blog->setParent(null == $page ? new Page() : $page);
        $blog->setCreatedAt(new \DateTime('now'));
        $blog->mergeNewTranslations();

        return $blog;
    }

    /**
     * @return \Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog
     */
    public function generateWordPressBlog()
    {
        $blog = new \Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog();
        $blog->setLocale('en');
        $blog->setTitle('Test Blog');
        $blog->setLink('http://www.testblog.com');
        $blog->setDescription('I test this blog');
        $blog->setPublicationDate(new \DateTime('Tue, 02 May 2017 13:56:23 +0000'));
        $blog->setBaseSiteUrl('http://www.testblog.com');
        $blog->setBaseBlogUrl('http://www.testblog.com');
        $blog->setId(1);
        $blog->setXmlTag('channel');

        return $blog;
    }
}
