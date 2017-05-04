<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Victoire\DevTools\VacuumBundle\Entity\WordPress\Article;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Curl\CurlsTools;
use Victoire\DevTools\VacuumBundle\Utils\Media\MediaFormater;

class VicArticleContentStages implements StageInterface
{
    /**
     * @var MediaFormater
     */
    private $mediaFormater;

    /**
     * VicArticleContentStages constructor.
     *
     * @param CurlsTools $curlsTools
     */
    public function __construct(
        MediaFormater $mediaFormater
    ) {
        $this->mediaFormater = $mediaFormater;
    }

    /**
     * Parse article content create Media entity for
     * picture found in it and update link accordingly.
     *
     * @param CommandPayloadInterface $payload
     *
     * @return CommandPayloadInterface
     */
    public function __invoke(CommandPayloadInterface $payload)
    {
        $progress = $payload->getNewProgressBar(count($payload->getTmpBlog()->getArticles()));
        $payload->getNewStageTitleMessage('Victoire Article Content generation:');

        foreach ($payload->getTmpBlog()->getArticles() as $plArticle) {
            $history = $payload->getXMLHistoryManager()->searchHistory($plArticle, \Victoire\Bundle\BlogBundle\Entity\Article::class);

            if (null == $history) {
                if (null != $plArticle->getContent()) {
                    $content = $plArticle->getContent();
                    $document = self::generateDOMDocument($content);

                    if ($document) {
                        $document = self::handleImg($document, $plArticle, $payload);
                    }

                    if ($document) {
                        $content = preg_replace('/^<!DOCTYPE.+?>/', '',
                            str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''),
                                $document->saveHTML()
                        ));

                        $plArticle->setContent($content);
                        $progress->advance();
                    }
                }
            }
        }

        $payload->jumpLine();
        $payload->getNewSuccessMessage(' success');

        return $payload;
    }

    /**
     * convert content in DOMDocument.
     *
     * @param $content
     *
     * @return bool|\DOMDocument
     */
    private function generateDOMDocument($content)
    {
        $document = new \DOMDocument();
        try {
            $document->loadHTML(
                mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'),
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );
        } catch (\Exception $e) {
            return false;
        } catch (\Throwable $e) {
            return false;
        }

        return $document;
    }

    /**
     * @param \DOMDocument $document
     *
     * @return bool
     */
    private function handleImg(\DOMDocument $document, Article $article, $payload)
    {
        if (null != $document->getElementsByTagName('img')) {
            $xpath = new \DOMXPath($document);
            $nodes = $xpath->query('//a//img');
            foreach ($nodes as $node) {
                $distantPath = $this->mediaFormater->cleanUrl($node->getAttribute('src'));

                if (null != $article->getAttachment()) {
                    $folder = $article->getAttachment()->getFolder();
                } else {
                    $blogFolder = $this->mediaFormater->generateBlogFolder($payload);
                    $folder = $this->mediaFormater->generateFoler($article->getTitle(), $blogFolder);
                }
                $image = $this->mediaFormater->generateImageMedia($distantPath, $folder);

                $node->setAttribute('src', $image->getUrl());
            }
        }

        return $document;
    }
}
