<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Utils\MockProvider;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

/**
 * Class DoctrineMockProvider.
 */
class DoctrineMockProvider extends TestCase
{
    /**
     * Will return an Moked EntityManager repository.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getEMMock($repositoryReturnValue = null)
    {
        $emMock = $this->createMock(EntityManager::class,
            ['getRepository', 'getClassMetadata', 'persist', 'flush'], [], '', false);

        if (null != $repositoryReturnValue && !is_array($repositoryReturnValue)) {
            $emMock->expects($this->any())
                ->method('getRepository')
                ->will($this->returnValue($this->getFakeRepository($repositoryReturnValue)));
        } elseif (is_array($repositoryReturnValue)) {
            $emMock->expects($this->any())
                ->method('getRepository')
                ->with($this->anything())
                ->will($this->returnCallback(function ($entityName) use ($repositoryReturnValue) {
                    foreach ($repositoryReturnValue as $returnValue) {
                        if ($entityName == $returnValue['entityName']) {
                            $fakeRepository = $this->createMock($returnValue['entityClass']);
                            $fakeRepository
                                ->method($returnValue['entityMethod'])
                                ->will($this->returnValue($returnValue['entityExpectedValue']));

                            return $fakeRepository;
                        }
                    }
                }));
        } else {
            $fakeRepository = $this->createMock(EntityRepository::class);

            $emMock->expects($this->any())
                ->method('getRepository')
                ->will($this->returnValue($fakeRepository));
        }

        $emMock->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue((object) ['name' => 'aClass']));

        $emMock->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(null));

        $emMock->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null));

        return $emMock;
    }

    /**
     * Return an Mocked EntityRepository.
     *
     * @param $expectedValue
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getFakeRepository($expectedValue)
    {
        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->method('findOneBy')
            ->will($this->returnValue($expectedValue));

        return $repository;
    }
}
