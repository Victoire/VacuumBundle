<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Utils\MockProvider;

use Victoire\DevTools\VacuumBundle\Utils\Media\MediaFormater;

class MediaFormaterMockProvider extends DoctrineMockProvider
{
    public function generateMediaFormaterMock()
    {
        $this->createMock(MediaFormater::class);
    }
}