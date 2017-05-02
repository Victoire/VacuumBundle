<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\Tag;

use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\Stages\Tag\TagDataExtractorStages;
use Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages\AbstractBaseStagesTests;

/**
 * Class TagDataExtractorStagesTest.
 */
class TagDataExtractorStagesTest extends AbstractBaseStagesTests
{
    public function testPayloadIntegrity()
    {
        $stage = new TagDataExtractorStages();
        $params = [];
        $xml = file_get_contents();
        $payload = $this->getFreshPayload($params, $xml);

        call_user_func($stage, $payload);
    }
}
