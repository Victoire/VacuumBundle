<?php
namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Author;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\Table;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\Author;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class AuthorDataExtractorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Author
 */
class AuthorDataExtractorStages implements PersisterStageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * AuthorDataExtractorStages constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Look for every Author in the dump inside current db.
     * If no match throw an error and ask for creation of author
     * account before blog import.
     *
     * @param $playload
     * @return mixed
     */
    public function __invoke(CommandPlayloadInterface $playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        $channel = $playload->getRawData()->channel;

        $progress = $playload->getNewProgressBar(count($channel->author));
        $playload->getNewSuccessMessage("Author data extraction:");

        $missingAuthor = [];

        foreach ($channel->author as $wpAuthor) {
            $email = $xmlDataFormater->formatString('author_email', $wpAuthor);

            $authorByUsername = $this->entityManager->getRepository('AppBundle:User\User')->findOneBy(['username' => $email]);
            $authorByEmail =  $this->entityManager->getRepository('AppBundle:User\User')->findOneBy(['email' => $email]);

            if (empty($authorByUsername) && empty($authorByEmail)) {

                $row = [
                    $xmlDataFormater->formatInteger('author_id', $wpAuthor),
                    $xmlDataFormater->formatString('author_login', $wpAuthor),
                    $xmlDataFormater->formatString('author_email', $wpAuthor),
                    $xmlDataFormater->formatString('author_display_name', $wpAuthor),
                    $xmlDataFormater->formatString('author_first_name', $wpAuthor),
                    $xmlDataFormater->formatString('author_last_name', $wpAuthor)
                ];
                array_push($missingAuthor, $row);
            } else {
                if (null != $authorByUsername) {
                    $playload->getTmpBlog()->addAuthors($authorByUsername);
                    $progress->advance();
                } elseif (null != $authorByEmail) {
                    $playload->getTmpBlog()->addAuthors($authorByEmail);
                    $progress->advance();
                }
            }
        }

        if (!empty($missingAuthor)) {
            $missingAuthorListe = new Table($playload->getOutput());
            $missingAuthorListe->setHeaders(['id', 'login', 'email', 'display name', 'firstname', 'lastname']);
            foreach ($missingAuthor as $author) {
                $missingAuthorListe->addRow($author);
            }
            $missingAuthorListe->render();
            $playload->throwErrorAndStop("Some Author can't be found ! Please create them before importing this blog again.");
        }

        $progress->finish();
        $playload->getNewSuccessMessage("success");

        unset($xmlDataFormater);
        return $playload;
    }
}