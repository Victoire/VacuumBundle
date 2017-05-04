<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Utils\MockProvider;

use Victoire\Bundle\MediaBundle\Entity\Folder;
use Victoire\Bundle\MediaBundle\Entity\Media;
use Victoire\DevTools\VacuumBundle\Utils\Media\MediaFormater;

/**
 * Class MediaFormaterMockProvider.
 */
class MediaFormaterMockProvider extends DoctrineMockProvider
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function generateMediaFormaterMock()
    {
        $mediaFormaterMock = $this->createMock(MediaFormater::class);

        $mediaFormaterMock
            ->method('generateBlogFolder')
            ->willReturn($this->createMock(Folder::class));

        $mediaFormaterMock
            ->method('generateFoler')
            ->willReturn($this->createMock(Folder::class));

        $mediaFormaterMock
            ->method('cleanUrl')
            ->willReturn('http://lorempixel.com/300/200/abstract');

        $mediaFormaterMock
            ->method('generateImageMedia')
            ->willReturn($this->createMock(Media::class));

        return $mediaFormaterMock;
    }
}
