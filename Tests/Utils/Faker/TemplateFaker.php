<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Utils\Faker;

use Victoire\Bundle\BlogBundle\Entity\ArticleTemplate;
use Victoire\Bundle\I18nBundle\Entity\ViewTranslation;

/**
 * Class TemplateFaker
 */
class TemplateFaker
{
    /**
     * @param $newVicBlog
     * @param array $widgetMaps
     * @return ArticleTemplate
     */
    public function generateVicArticleTemplate($newVicBlog, array $widgetMaps)
    {
        $template = new ArticleTemplate();
        $template->setName('{{item.name}}', "en");
        $template->setSlug('{{item.slug}}', "en");
        $template->setBusinessEntityId('article');
        $template->setBackendName("blog_test_article_template");
        $template->setLayout("oneCol_test_layout");
        $template->setParent($newVicBlog);

        $translation = new ViewTranslation();
        $translation->setLocale("en");
        $translation->setName("{{item.name}}");
        $translation->setSlug("{{item.slug}}");
        $template->addTranslation($translation);
        $template->mergeNewTranslations();

        foreach ($widgetMaps as $widgetMap) {
            $template->addWidgetMap($widgetMap);
        }

        return $template;
    }
}