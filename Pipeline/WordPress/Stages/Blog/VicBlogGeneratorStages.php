<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Blog;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Blog;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;

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
     * @param $playload
     * @return mixed
     */
    public function __invoke(PlayloadInterface $playload)
    {
        $playload->getOutput()->write(sprintf('Victoire Blog generation:'));

        $blog = new Blog();
        $blog->setName($playload->getTitle(), $playload->getLocale());
        $blog->setCurrentLocale($playload->getLocale());
        $blog->setDefaultLocale($playload->getLocale());
        $blog->setTemplate(self::getBaseTemplate(1));
        $blog->setParent(self::getParentPage(8));
        $blog->setPublishedAt($playload->getPublicationDate());
        $blog->setCreatedAt($playload->getPublicationDate());
        $playload->setNewBlog($blog);

        foreach ($blog->getTranslations() as $key => $translation) {
            if ($key != $playload->getLocale()) {
                $blog->removeTranslation($translation);
            }
        }

        $this->entityManager->persist($blog);
        $playload->getOutput()->writeln(' success');
        return $playload;
    }
}