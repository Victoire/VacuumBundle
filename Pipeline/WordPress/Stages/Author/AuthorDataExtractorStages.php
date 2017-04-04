<?php
namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Author;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\Table;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\Author;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
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
     * @param $playload
     * @return mixed
     */
    public function __invoke(PlayloadInterface $playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        foreach ($playload->getRawData()->channel as $blog) {

            $progress = $playload->getProgressBar(count($blog->author));
            $playload->getOutput()->writeln(sprintf('Author data extraction:'));

            $missingAuthor = [];

            foreach ($blog->author as $wpAuthor) {
                $email = $xmlDataFormater->formatString('author_email', $wpAuthor);

                $authorByUsername = $this->entityManager->getRepository('AppBundle:User\User')->findBy(['username' => $email]);
                $authorByEmail =  $this->entityManager->getRepository('AppBundle:User\User')->findBy(['email' => $email]);

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
                        $playload->addAuthor($authorByUsername[0]);
                        $progress->advance();
                    } elseif (null != $authorByEmail) {
                        $playload->addAuthoy($authorByEmail[0]);
                        $progress->advance();
                    }
                }
            }

            if (!empty($missingAuthor)) {
                $playload->getOutput()->writeln("<error>Some Author can't be found ! Please create them before importing this blog again.</error>");
                $missingAuthorListe = new Table($playload->getOutput());
                $missingAuthorListe->setHeaders(['id', 'login', 'email', 'display name', 'firstname', 'lastname']);
                foreach ($missingAuthor as $author) {
                    $missingAuthorListe->addRow($author);
                }
                $missingAuthorListe->render();
                exit();
            }
        }

        $progress->finish();
        $playload->getSuccess();

        unset($xmlDataFormater);
        return $playload;
    }
}