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
                if (!file_exists($this->kernelRootDir."/../web/uploads/blog")) {
                    mkdir($this->kernelRootDir."/../web/uploads/blog");
                }
                $path = $plArticle->getAttachmentUrl();
                $fileName = explode("/", $path);
                $fileName = end($fileName);

                try {
                    $filePath = sprintf("%s/../web/uploads/blog/%s", $this->kernelRootDir, $fileName);
                    copy($plArticle->getAttachmentUrl(), $filePath);
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