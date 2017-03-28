<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Behat\Mink\Exception\Exception;
use Victoire\DevTools\VacuumBundle\Pipeline\FileStageInterface;

class VicArticleContentStages implements FileStageInterface
{
    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * VicArticleContentStages constructor.
     * @param $kernerRootDir
     */
    public function __construct($kernerRootDir)
    {
        $this->kernelRootDir = $kernerRootDir;
    }

    public function __invoke($playload)
    {
        foreach ($playload->getItems() as $plArticle) {
            if (null != $plArticle->getContent()) {

                $content = $plArticle->getContent();
                $document = self::generateDOMDocument($content);

                if ($document) {
                    self::handleImg($document);
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
    private function handleImg(\DOMDocument $document) {

        if (null != $document->getElementsByTagName("img")) {
            $xpath = new \DOMXPath($document);
            $nodes = $xpath->query("//a//img");
            foreach ($nodes as $node) {
                $distantPath = $node->getAttribute('src');
                $fileName = explode("/", $distantPath);
                $fileName = end($fileName);
                try {
                    $filePath = sprintf("%s/../web/uploads/blog/%s", $this->kernelRootDir, $fileName);
                    copy($distantPath, $filePath);
                } catch (\Exception $e) {
                    return false;
                } catch (\Throwable $e) {
                    return false;
                }
                $a = $xpath->query("//a//img/preceding::a[1]");
                foreach ($a as $link) {
                    if ($link->getAttribute('href') == $distantPath) {
                        $link->setAttribute('href', $filePath);
                    }
                }
                $node->setAttribute('src', $filePath);
            }
        }
    }
}