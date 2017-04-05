<?php

namespace Victoire\DevTools\VacuumBundle\Utils\Media;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\MediaBundle\Entity\Folder;
use Victoire\Bundle\MediaBundle\Entity\Media;
use Victoire\DevTools\VacuumBundle\Playload\CommandPlayloadInterface;
use Victoire\DevTools\VacuumBundle\Utils\Curl\CurlsTools;

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
     * @var CurlsTools
     */
    private $curlsTools;

    /**
     * MediaFormater constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager,
        $kernelRootDir,
        CurlsTools $curlsTools
    )
    {
        $this->entityManager = $entityManager;
        $this->kernelRootDir = $kernelRootDir;
        $this->curlsTools = $curlsTools;
    }

    /**
     * @param $playload
     * @return mixed
     */
    public function generateBlogFolder(CommandPlayloadInterface $playload)
    {
        if (null == $playload->getTmpBlog()->getBlogFolder()) {
            $blogFolder = $this->generateFoler("blog");
            $playload->getTmpBlog()->setBlogFolder($blogFolder);
        } else {
            $blogFolder = $playload->getTmpBlog()->getBlogFolder();
        }

        return $blogFolder;
    }

    /**
     * @param $distantPath
     * @return mixed|string
     */
    public function cleanUrl($distantPath)
    {
        $distantPath = trim($distantPath);
        $url = parse_url($distantPath);
        $distantPath = sprintf("%s://%s%s",
            $url['scheme'],
            $url['host'],
            urlencode($url['path'])
        );
        $distantPath = str_replace("%2F","/", $distantPath);

        return $distantPath;
    }

    /**
     * @param $newFolderName
     * @param $parentFolderName
     * @return mixed
     */
    public function generateFoler($newFolderName, $parentFolderName = null)
    {
        if (!file_exists($this->kernelRootDir."/../web/uploads/media")) {
            mkdir($this->kernelRootDir."/../web/uploads/media", 0777, true);
        }

        $folder = new Folder();
        $folder->setName($newFolderName != null ? $newFolderName : "unknown");
        if ($parentFolderName == null) {
            $parentFolderName = $this->entityManager->getRepository("VictoireMediaBundle:Folder")->find(1);
        }
        $folder->setParent($parentFolderName);
        $folder->setRel("media");
        $this->entityManager->persist($folder);

        return $folder;
    }

    /**
     * @param $path
     * @return Media
     */
    public function generateImageMedia($path, Folder $folder)
    {
        if (null != $path) {

            $attachment = new Media();
            $distantPath = $path;
            $fileName = explode("/", $distantPath);
            $fileName = end($fileName);
            $attachment->setName($fileName);
            $fileExtension = null;
            $fileExtension = explode(".", $fileName);
            $fileExtension = end($fileExtension);
            // this persist will generate the uuid for attachment
            $this->entityManager->persist($attachment);
            $fileName = $attachment->getUuid().".".$fileExtension;

            $filePath = $this->curlsTools->getDistantPicture($fileName, $distantPath);

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $attachment->setContentType(finfo_file($finfo, $filePath));
            $attachment->setLocation("local");
            $attachment->setUrl('/uploads/media/'.$fileName);
            $attachment->setFolder($folder);

            $this->entityManager->persist($attachment);
            return $attachment;
        }
    }
}