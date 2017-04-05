<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Category;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;

/**
 * Class VicCategoryGeneratorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category
 */
class VicCategoryGeneratorStages implements PersisterStageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * CategoryGeneratorStages constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $playload
     * @return mixed
     */
    public function __invoke(CommandPlayloadInterface $playload)
    {
        $progress = $playload->getNewProgressBar(count($playload->getTmpBlog()->getCategories()));
        $playload->getNewStageTitleMessage('Victoire Category generation:');

        foreach ($playload->getTmpBlog()->getCategories() as $plCategory) {
            $category = new Category();
            $category->setTitle($plCategory->getCategoryName());
            $category->setSlug($plCategory->getCategoryNiceName());
            $playload->getNewVicBlog()->addCategorie($category);

            $this->entityManager->persist($category);
            $progress->advance();
        }

        $progress->finish();
        $playload->getNewSuccessMessage(" success");
        $playload->jumpLine();

        return $playload;
    }
}