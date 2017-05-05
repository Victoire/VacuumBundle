<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Utils\Faker;

use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;
use Victoire\Widget\CKEditorBundle\Entity\WidgetCKEditor;
use Victoire\Widget\LayoutBundle\Entity\WidgetLayout;

/**
 * Class WidgetFaker
 */
class WidgetFaker
{
    /**
     * @return array
     */
    public function generateWidget()
    {
        $widgetMapLayout = new WidgetMap();
        $widgetMapLayout->setAction(WidgetMap::ACTION_CREATE);
        $widgetMapLayout->setSlot("main_content");

        $widgetLayout = new WidgetLayout();
        $widgetLayout->setWidgetMap($widgetMapLayout);
        $widgetLayout->setLayoutMd('once');
        $widgetLayout->setHasContainer(true);

        $widgetMapCKEditor = new WidgetMap();
        $widgetMapCKEditor->setAction(WidgetMap::ACTION_CREATE);
        $widgetMapCKEditor->setSlot($widgetLayout->getChildrenSlot().'_1');

        $widgetCKEditor = new WidgetCKEditor();
        $widgetCKEditor->setWidgetMap($widgetMapCKEditor);

        return $widgets = [$widgetMapLayout, $widgetMapCKEditor];
    }
}