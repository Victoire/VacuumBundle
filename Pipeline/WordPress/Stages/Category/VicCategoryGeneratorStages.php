<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Category;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\BlogCategory;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;

/**
 * Class VicCategoryGeneratorStages.
 */
class VicCategoryGeneratorStages implements PersisterStageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * CategoryGeneratorStages constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $payload
     *
     * @return mixed
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $progress = $payload->getNewProgressBar(count($payload->getTmpBlog()->getCategories()));

        $payload->getNewStageTitleMessage('Victoire Category generation:');

        foreach ($payload->getTmpBlog()->getCategories() as $plCategory) {
            $payload->getXMLHistoryManager()->reload();
            $history = $payload->getXMLHistoryManager()->searchHistory($plCategory, Category::class);

            if (null == $history) {
                $category = new BlogCategory();
                $category->setTitle($plCategory->getCategoryName());
                $category->setSlug($plCategory->getCategoryNiceName());
                $payload->getNewVicBlog()->addCategory($category);
                $history = $payload->getXMLHistoryManager()->generateHistory($plCategory, $category);
                $payload->getXMLHistoryManager()->flushHistory($category, $history);
                $progress->advance();
            }
        }

        $payload->getNewSuccessMessage(' success');
        $payload->jumpLine();

        return $payload;
    }
}
