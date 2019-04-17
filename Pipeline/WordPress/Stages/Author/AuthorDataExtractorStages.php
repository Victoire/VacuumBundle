<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Author;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\Table;
use Victoire\Bundle\UserBundle\Entity\User;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\Author;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class AuthorDataExtractorStages.
 */
class AuthorDataExtractorStages implements PersisterStageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * AuthorDataExtractorStages constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Look for every Author in the dump, inside current db.
     * If there is no match, it throw an error and ask for creation of author
     * account before blog import.
     *
     * @param $payload
     *
     * @return mixed
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $xmlDataFormater = new XmlDataFormater();

        $channel = $payload->getRawData()->channel;

        $progress = $payload->getNewProgressBar(count($channel->author));
        $payload->getNewStageTitleMessage('Author data extraction:');

        $missingAuthor = [];

        foreach ($channel->author as $wpAuthor) {
            $email = $xmlDataFormater->formatString('author_email', $wpAuthor);

            $authorByUsername = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $email]);
            $authorByEmail = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (empty($authorByUsername) && empty($authorByEmail)) {
                $row = [
                    $xmlDataFormater->formatInteger('author_id', $wpAuthor),
                    $xmlDataFormater->formatString('author_login', $wpAuthor),
                    $xmlDataFormater->formatString('author_email', $wpAuthor),
                    $xmlDataFormater->formatString('author_display_name', $wpAuthor),
                    $xmlDataFormater->formatString('author_first_name', $wpAuthor),
                    $xmlDataFormater->formatString('author_last_name', $wpAuthor),
                ];
                array_push($missingAuthor, $row);
            } else {
                if (null != $authorByUsername) {
                    $payload->getTmpBlog()->addAuthors($authorByUsername);
                    $progress->advance();
                } elseif (null != $authorByEmail) {
                    $payload->getTmpBlog()->addAuthors($authorByEmail);
                    $progress->advance();
                }
            }
        }

        if (!empty($missingAuthor)) {
            $missingAuthorListe = new Table($payload->getOutput());
            $missingAuthorListe->setHeaders(['id', 'login', 'email', 'display name', 'firstname', 'lastname']);
            foreach ($missingAuthor as $author) {
                $missingAuthorListe->addRow($author);
            }
            $missingAuthorListe->render();
            $payload->throwErrorAndStop("Some Author can't be found ! Please create them before importing this blog again.");
        }

        $progress->finish();
        $payload->getNewSuccessMessage(' success');
        $payload->jumpLine();

        unset($xmlDataFormater);

        return $payload;
    }
}
