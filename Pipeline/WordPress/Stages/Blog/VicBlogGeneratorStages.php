<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Blog;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;

/**
 * Class VicBlogGeneratorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog
 */
class VicBlogGeneratorStages implements PersisterStageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * VicBlogGeneratorStages constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $id
     * @return null|object|\Victoire\Bundle\TemplateBundle\Entity\Template
     */
    private function getBaseTemplate($id)
    {
        $template = $this->entityManager->getRepository('VictoireTemplateBundle:Template')->find($id);
        return $template;
    }

    /**
     * @param $id
     * @return null|object|\Victoire\Bundle\PageBundle\Entity\Page
     */
    private function getParentPage($id)
    {
        $page = $this->entityManager->getRepository('VictoirePageBundle:Page')->find($id);
        return $page;
    }

    /**
     * Instantiate an new VictoireBlog and hydrate it with base info
     * from tmpBlog, then persist it.
     *
     * @param $playload
     * @return mixed
     */
    public function __invoke(CommandPlayloadInterface $playload)
    {
        $parameters = $playload->getParameters();

        $playload->getNewStageTitleMessage("Victoire Blog generation:");

        $blog = new Blog();
        $blog->setDefaultLocale($playload->getTmpBlog()->getLocale());
        $blog->setCurrentLocale($blog->getDefaultLocale());
        $blog->setName($parameters['blog_name'], $blog->getDefaultLocale());
        $blog->setTemplate(self::getBaseTemplate($parameters['blog_template']));
        $blog->setParent(self::getParentPage($parameters['blog_parent_id']));
        $blog->setPublishedAt($playload->getTmpBlog()->getPublicationDate());
        $blog->setCreatedAt($playload->getTmpBlog()->getPublicationDate());
        $playload->setNewVicBlog($blog);

        foreach ($blog->getTranslations() as $key => $translation) {
            if ($key != $blog->getDefaultLocale()) {
                $blog->removeTranslation($translation);
            }
        }

        $this->entityManager->persist($blog);
        $playload->getNewSuccessMessage(" success");
        $playload->jumpLine();

        return $playload;
    }
}