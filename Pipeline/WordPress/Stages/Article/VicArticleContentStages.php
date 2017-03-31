<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Behat\Mink\Exception\Exception;
use Victoire\DevTools\VacuumBundle\Pipeline\FileStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Curl\CurlsTools;

class VicArticleContentStages implements StageInterface
{
    /**
     * @var string
     */
    private $curlsTools;

    /**
     * VicArticleContentStages constructor.
     * @param CurlsTools $curlsTools
     */
    public function __construct(
        CurlsTools $curlsTools
    )
    {
        $this->curlsTools = $curlsTools;
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
                    $document = self::handleImg($document);
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

                $this->curlsTools->getDistantPicture($fileName, $distantPath);

                $a = $xpath->query("//a//img/preceding::a[1]");
                foreach ($a as $link) {

                    $linkFileName = $link->getAttribute('href');
                    $linkFileName = explode("/", $linkFileName);
                    $linkFileName = end($linkFileName);
                    $linkFileName = str_replace(['.jpg','.png','.gif'],"", $linkFileName);

                    if (null != $linkFileName) {
                        if (strpos($distantPath, $linkFileName)) {
                            $link->setAttribute('href', '/uploads/media/blog/'.$fileName);
                        }
                    }
                }
                $node->setAttribute('src', '/uploads/media/blog/'.$fileName);
            }
        }

        return $document;
    }
}