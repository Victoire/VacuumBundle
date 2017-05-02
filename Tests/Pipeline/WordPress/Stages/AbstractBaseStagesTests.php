<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages;

//use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayload;
use Victoire\DevTools\VacuumBundle\Utils\History\XMLHistoryManager;

/**
 * Class AbstractBaseStagesTests
 * @package Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages
 */
class AbstractBaseStagesTests extends \PHPUnit_Framework_TestCase
{
    /**
     * @return CommandPayload
     */
    public function getFreshPayload(array $params, $xml)
    {
        $output = $this->createMock(OutputInterface::class);
        $questionHelper = $this->createMock(QuestionHelper::class);
        $rawData = simplexml_load_string($xml);
        $xmlHistory = $this->createMock(XMLHistoryManager::class);

        return $payload = new CommandPayload(
            $params,
            $output,
            $questionHelper,
            $rawData,
            $xmlHistory
        );
    }
}