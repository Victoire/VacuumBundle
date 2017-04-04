<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\SEO;

use Doctrine\ORM\EntityManager;
use Victoire\Bundle\SeoBundle\Entity\PageSeo;
use Victoire\DevTools\VacuumBundle\Pipeline\PersisterStageInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\PlayloadInterface;
use Victoire\DevTools\VacuumBundle\Pipeline\StageInterface;
use Victoire\DevTools\VacuumBundle\Utils\Xml\XmlDataFormater;

class VicSEOGenerator implements PersisterStageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * VicSEOGenerator constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(PlayloadInterface $playload)
    {
        $xmlDataFormater = new XmlDataFormater();
        $articles = $this->entityManager->getRepository('VictoireBlogBundle:Article')->findAll();

        $progress = $playload->getProgressBar();
        $playload->getOutput()->writeln(sprintf('Victoire SEO generation:'));

        foreach ($articles as $article) {
            foreach ($playload->getRawData()->channel as $blog) {
                foreach ($blog->item as $wpArticle) {
                    if ($article->getName() == $xmlDataFormater->formatString('title', $wpArticle) ) {
                        if (count($wpArticle->postmeta > 2)) {
                            $seo = new PageSeo();
                            $seo->setDefaultLocale($playload->getLocale());
                            foreach ($wpArticle->postmeta as $meta) {

                                $key = $xmlDataFormater->formatString('meta_key', $meta);
                                $value = $xmlDataFormater->formatString('meta_value', $meta);

                                switch ($key) {
                                    case ("_yoast_wpseo_title"):
                                        $seo->setMetaTitle($value, $playload->getLocale());
                                        break;
                                    case ("_yoast_wpseo_metadesc"):
                                        $seo->setMetaDescription($value, $playload->getLocale());
                                        break;
                                    case ("_yoast_wpseo_meta-robots-noindex"):
                                        $value = (boolean)$value;
                                        if ($value) {
                                            $value = "index";
                                        } else {
                                            $value = "noindex";
                                        }
                                        $seo->setMetaRobotsIndex($value, $playload->getLocale());
                                        break;
                                    case ("_yoast_wpseo_meta-robots-nofollow"):
                                        $value = (boolean)$value;
                                        if ($value) {
                                            $value = "follow";
                                        } else {
                                            $value = "nofollow";
                                        }
                                        $seo->setMetaRobotsFollow($value, $playload->getLocale());
                                        break;
                                    case ("_yoast_wpseo_meta-robots-adv"):
                                        if ($value == "none") {
                                            $value = null;
                                        }
                                        $seo->setMetaRobotsAdvanced($value, $playload->getLocale());
                                        break;
                                    case ("_yoast_wpseo_sitemap-include"):
                                        if ($value != "-") {
                                            $value = (boolean)$value;
                                            $seo->setSitemapIndexed($value, $playload->getLocale());
                                        }
                                        break;
                                    case ("_yoast_wpseo_sitemap-prio"):
                                        if ($value != "-") {
                                            $seo->setSitemapPriority($value, $playload->getLocale());
                                        }
                                        break;
                                    case ("_yoast_wpseo_canonical"):
                                        $seo->setRelCanonical($value, $playload->getLocale());
                                        break;
                                    case ("_yoast_wpseo_opengraph-description"):
                                        $seo->setOgDescription($value, $playload->getLocale());
                                        break;
                                    case ("_thumbnail_id"):
                                        $seo->setOgImage($article->getImage(), $playload->getLocale());
                                        break;
                                }
                            }
                        } else {
                            $seo = null;
                        }

                        if (null != $seo) {

                            $keyword = "";
                            foreach ($article->getTags() as $tag) {
                                $keyword .= $tag->getTitle().",";
                            }
                            $seo->setKeyword($keyword, $playload->getLocale());

                            $ep = $this->entityManager
                                ->getRepository('Victoire\Bundle\CoreBundle\Entity\EntityProxy')
                                ->findOneBy(['article' => $article->getId()]);
                            $bp = $this->entityManager->getRepository('VictoireBusinessPageBundle:BusinessPage')
                                ->findOneBy(['entityProxy' => $ep->getId()]);
                            $bp->setSeo($seo);
                            $this->entityManager->persist($bp);
                            $progress->advance();
                        }
                    }
                }
            }
        }
        $playload->getSuccess();
        return $playload;
    }
}