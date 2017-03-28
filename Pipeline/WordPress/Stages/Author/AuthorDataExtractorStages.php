<?php
namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Author;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Author;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

/**
 * Class AuthorDataExtractorStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Author
 */
class AuthorDataExtractorStages implements StageInterface
{
    /**
     * @param $playload
     * @return mixed
     */
    public function __invoke($playload)
    {
        $xmlDataFormater = new XmlDataFormater();

        foreach ($playload->getRawData()->channel as $blog) {
            foreach ($blog->author as $wpAuthor) {
                $author = new  Author();

                $author->setWpAuthorId($xmlDataFormater->formatInteger('author_id', $wpAuthor));
                $author->setAuthorLogin($xmlDataFormater->formatString('author_login', $wpAuthor));
                $author->setAuthorEmail($xmlDataFormater->formatString('author_email', $wpAuthor));
                $author->setAuthorDisplayName($xmlDataFormater->formatString('author_display_name', $wpAuthor));
                $author->setAuthorFirstName($xmlDataFormater->formatString('author_first_name', $wpAuthor));
                $author->setAuthorLastName($xmlDataFormater->formatString('author_last_name', $wpAuthor));

                $playload->addAuthor($author);
            }
        }

        unset($xmlDataFormater);
        return $playload;
    }
}