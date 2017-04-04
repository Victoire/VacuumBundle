<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Category;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;

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
    public function __invoke(PlayloadInterface $playload)
    {
        $progress = $playload->getProgressBar(count($playload->getCategories()));
        $playload->getOutput()->writeln(sprintf('Victoire Category generation:'));
        foreach ($playload->getCategories() as $plCategory) {
            $category = new Category();
            $category->setTitle($plCategory->getCategoryName());
            $category->setSlug($plCategory->getCategoryNiceName());
            $playload->getNewBlog()->addCategorie($category);

            $this->entityManager->persist($category);
            $progress->advance();
        }

        $progress->finish();
        $playload->getSuccess();

        return $playload;
    }
}