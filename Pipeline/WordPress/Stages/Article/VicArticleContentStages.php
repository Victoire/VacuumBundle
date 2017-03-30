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
                try {
                    if (!file_exists($this->kernelRootDir."/../web/uploads/blog/article_content")) {
                        mkdir($this->kernelRootDir."/../web/uploads/blog/article_content", 0777, true);
                    }
                    $filePath = sprintf("%s/../web/uploads/blog/article_content/%s", $this->kernelRootDir, $fileName);
                    if (!file_exists($filePath)) {
                        $lfile = fopen($filePath, "w");

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $distantPath);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');
                        curl_setopt($ch, CURLOPT_FILE, $lfile);

                        fclose($lfile);
                        curl_close($ch);
                    }
                } catch (\Exception $e) {
                    return false;
                } catch (\Throwable $e) {
                    return false;
                }

                $a = $xpath->query("//a//img/preceding::a[1]");
                foreach ($a as $link) {

                    $linkFileName = $link->getAttribute('href');
                    $linkFileName = explode("/", $linkFileName);
                    $linkFileName = end($linkFileName);
                    $linkFileName = str_replace(['.jpg','.png','.gif'],"", $linkFileName);

                    if (null != $linkFileName) {
                        if (strpos($distantPath, $linkFileName)) {
                            $link->setAttribute('href', '/uploads/blog/article_content/'.$fileName);
                        }
                    }
                }
                $node->setAttribute('src', '/uploads/blog/article_content/'.$fileName);
            }
        }

        return $document;
    }
}