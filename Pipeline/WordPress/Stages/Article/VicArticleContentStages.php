<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Behat\Mink\Exception\Exception;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\Article;
use Victoire\DevTools\VacuumBundle\Pipeline\FileStageInterface;
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
     * @param CurlsTools $curlsTools
     */
    public function __construct(
        MediaFormater $mediaFormater
    )
    {
        $this->mediaFormater = $mediaFormater;
    }

    /**
     * @param $playload
     * @return mixed
     */
    public function __invoke($playload)
    {
        foreach ($playload->getItems() as $plArticle) {
            if (null != $plArticle->getContent()) {

                $content = $plArticle->getContent();
                $document = self::generateDOMDocument($content);

                if ($document) {
                    $document = self::handleImg($document, $plArticle, $playload);
                }

                if ($document) {
                    $content = $document->saveHTML();
                    $plArticle->setContent($content);
                }
            }
        }

        return $playload;
    }

    /**
     * @param $content
     * @return bool|\DOMDocument
     */
    private function generateDOMDocument($content) {
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
     * @return bool
     */
    private function handleImg(\DOMDocument $document, Article $article, $playload) {

        if (null != $document->getElementsByTagName("img")) {
            $xpath = new \DOMXPath($document);
            $nodes = $xpath->query("//a//img");
            foreach ($nodes as $node) {

                $distantPath = $this->mediaFormater->cleanUrl($node->getAttribute('src'));

                if (null != $article->getAttachment()) {
                    $folder = $article->getAttachment()->getFolder();
                } else {
                    $blogFolder = $this->mediaFormater->generateBlogFolder($playload);
                    $folder = $this->mediaFormater->generateFoler($article->getTitle(), $blogFolder);
                }
                $image = $this->mediaFormater->generateImageMedia($distantPath, $folder);

                $node->setAttribute('src', $image->getUrl());
            }
        }

        return $document;
    }
}