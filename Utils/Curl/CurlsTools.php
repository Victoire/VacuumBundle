<?php

namespace Victoire\DevTools\VacuumBundle\Utils\Curl;

/**
 * Class CurlsTools
 * @package Victoire\DevTools\VacuumBundle\Utils\Curl
 */
class CurlsTools
{
    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * CurlsTools constructor.
     * @param $kernelRootDir
     */
    public function __construct(
        $kernelRootDir
    )
    {
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * @param $fileName
     * @param $directory
     * @param $distantPath
     * @return bool|string
     */
    public function getDistantPicture($fileName, $distantPath)
    {
        try {

            $filePath = sprintf(
                "%s/../web/uploads/media/%s",
                    $this->kernelRootDir,
                    $fileName
            );

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
                curl_close($ch);
            }

            return $filePath;

        } catch (\Exception $e) {
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }
}