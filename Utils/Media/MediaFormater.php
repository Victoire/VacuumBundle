<?php

namespace Victoire\DevTools\VacuumBundle\Utils\Media;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\MediaBundle\Entity\Media;

class MediaFormater
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * MediaFormater constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager,
        $kernelRootDir
    )
    {
        $this->entityManager = $entityManager;
        $this->kernelRootDir = $kernelRootDir;
    }

    public function generateImageMedia($path)
    {
        if (null != $path) {
            if (!file_exists($this->kernelRootDir."/../web/uploads/blog/article_header")) {
                mkdir($this->kernelRootDir."/../web/uploads/blog/article_header", 0777, true);
            }
            $attachment = new Media();
            $distantPath = $path;
            $fileName = explode("/", $distantPath);
            $fileName = end($fileName);
            $attachment->setName($fileName);
            $fileExtension = null;
            $fileExtension = explode(".", $fileName);
            $fileExtension = end($fileExtension);
            $this->entityManager->persist($attachment);
            $fileName = $attachment->getUuid().".".$fileExtension;


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

                    curl_exec($ch);

                    fclose($lfile);
                    $error = curl_error($ch);
                    curl_close($ch);
                }

            } catch (\Exception $e) {
                return null;
            } catch (\Throwable $e) {
                return null;
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $attachment->setContentType(finfo_file($finfo, $filePath));
            $attachment->setLocation("local");
            $attachment->setUrl('/uploads/blog/article_header/'.$fileName);

            $this->entityManager->persist($attachment);
            return $attachment;
        }
    }
}