<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\BlogBundle\Entity\Tag;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;

/**
 * Class VicTagGeneratorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag
 */
class VicTagGeneratorStages implements PersisterStageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * TagGeneratorStages constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transfer tag form tmpBlog to Victoire blog.
     *
     * @param $payload
     * @return mixed
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $progress = $payload->getNewProgressBar(count($payload->getTmpBlog()->getTags()));
        $payload->getNewStageTitleMessage("Victoire Tag generation:");
        $payload->getXMLHistoryManager()->reload();

        foreach ($payload->getTmpBlog()->getTags() as $wpTag) {

            $history = $payload->getXMLHistoryManager()->searchHistory($wpTag, Tag::class);

            if (null == $history) {
                $tag = new Tag();
                $tag->setTitle($wpTag->getTagName());
                $tag->setSlug(($wpTag->getTagSlug()));
                $payload->getNewVicBlog()->addTag($tag);
                $history = $payload->getXMLHistoryManager()->generateHistory($wpTag, $tag);
                $payload->getXMLHistoryManager()->flushHistory($tag, $history);
                $progress->advance();
            }
        }

        $payload->getNewSuccessMessage(" success");
        $payload->jumpLine();

        return $payload;
    }
}