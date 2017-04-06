<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Category;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;

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
     * @param $payload
     * @return mixed
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $progress = $payload->getNewProgressBar(count($payload->getTmpBlog()->getCategories()));
        $payload->getNewStageTitleMessage('Victoire Category generation:');

        foreach ($payload->getTmpBlog()->getCategories() as $plCategory) {
            $category = new Category();
            $category->setTitle($plCategory->getCategoryName());
            $category->setSlug($plCategory->getCategoryNiceName());
            $payload->getNewVicBlog()->addCategorie($category);

            $this->entityManager->persist($category);
            $progress->advance();
        }

        $progress->finish();
        $payload->getNewSuccessMessage(" success");
        $payload->jumpLine();

        return $payload;
    }
}