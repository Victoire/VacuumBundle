<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\Author;

use Victoire\Bundle\UserBundle\Entity\User;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Author\AuthorDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\AbstractBaseStagesTests;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\ArticleFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\Faker\BlogFaker;
use Victoire\DevTools\VacuumBundle\Tests\Utils\MockProvider\DoctrineMockProvider;

/**
 * Class AuthorDataExtractorStagesTest.
 */
class AuthorDataExtractorStagesTest extends AbstractBaseStagesTests
{
    public function testPayloadIntegrity()
    {
        $doctrineMockProvider = new DoctrineMockProvider();
        $author = new User();
        $author->setEmail('author1@blogtest.com');
        $author->setUsername('author1@blogtest.com');
        $entityManager = $doctrineMockProvider->getEMMock($author);

        $stage = new AuthorDataExtractorStages($entityManager);
        $params = [];
        $xml = file_get_contents('Tests/Resources/xml/author/author_data_extraction.xml');

        $payload = $this->getFreshPayload($params, $xml, new Blog());

        $payload = call_user_func($stage, $payload);

        $this->assertSame($author, $payload->getTmpBlog()->getAuthors()[0]);
    }

    public function testUnknownAuthorError()
    {
        $doctrineMockProvider = new DoctrineMockProvider();
        $entityManager = $doctrineMockProvider->getEMMock();

        $stage = new AuthorDataExtractorStages($entityManager);
        $params = [];
        $xml = file_get_contents('Tests/Resources/xml/author/author_data_extraction.xml');
        $payload = $this->getFreshPayload($params, $xml, new Blog());

        try {
            call_user_func($stage, $payload);
        } catch (\Throwable $e) {
            $this->assertEquals(
                "Some Author can't be found ! Please create them before importing this blog again.",
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->assertEquals(
                "Some Author can't be found ! Please create them before importing this blog again.",
                $e->getMessage()
            );
        }
    }
}
