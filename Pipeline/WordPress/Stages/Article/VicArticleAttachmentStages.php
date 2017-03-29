<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article;

use Victoire\DevTools\VacuumBundle\Pipeline\FileStageInterface;

/**
 * Class VicArticleMediaBuilderStages
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Article
 */
class VicArticleAttachmentStages implements FileStageInterface
{
    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * VicArticleMediaBuilderStages constructor.
     * @param $kernerRootDir
     */
    public function __construct($kernerRootDir)
    {
        $this->kernelRootDir = $kernerRootDir;
    }

    /**
     * @param $playload
     */
    public function __invoke($playload)
    {
        foreach ($playload->getItems() as $plArticle) {
            if (null != $plArticle->getAttachmentUrl()) {
                if (!file_exists($this->kernelRootDir."/../web/uploads/blog/article_header")) {
                    mkdir($this->kernelRootDir."/../web/uploads/blog/article_header", 0777, true);
                }
                $distantPath = $plArticle->getAttachmentUrl();
                $fileName = explode("/", $distantPath);
                $fileName = end($fileName);

                try {
                    $filePath = sprintf("%s/../web/uploads/blog/article_header/%s", $this->kernelRootDir, $fileName);
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
                    $plArticle->setAttachmentUrl($filePath);
                } catch (\Exception $e) {
                    $plArticle->setAttachmentUrl(null);
                } catch (\Throwable $e) {
                    $plArticle->setAttachmentUrl(null);
                }
            }
        }
        return $playload;
    }
}