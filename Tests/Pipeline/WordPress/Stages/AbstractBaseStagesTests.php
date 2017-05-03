<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Victoire\DevTools\VacuumBundle\Entity\VacuumXMLRelationHistory;
use Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog;
use Victoire\DevTools\VacuumBundle\Payload\CommandPayload;
use Victoire\DevTools\VacuumBundle\Utils\History\XMLHistoryManager;

/**
 * Class AbstractBaseStagesTests.
 */
class AbstractBaseStagesTests extends TestCase
{
    /**
     * @param array $params {
     *                      $params = [
     * @param $xml
     *
     * @return CommandPayload
     */
    public function getFreshPayload(array $params, $xml, Blog $blog = null)
    {
        $output = $this->createMock(OutputInterface::class);
        $output->method('getFormatter')->willReturn($this->createMock(OutputFormatterInterface::class));
        $questionHelper = $this->createMock(QuestionHelper::class);
        $rawData = simplexml_load_string($xml);

        $xmlHistory = $this->createMock(XMLHistoryManager::class);
        $xmlHistory
            ->method("generateHistory")
            ->willReturn(new VacuumXMLRelationHistory());

        $payload = new CommandPayload(
            $params,
            $output,
            $questionHelper,
            $rawData,
            $xmlHistory
        );

        if (null != $blog) {
            $payload->setTmpBlog($blog);
        }

        return $payload;
    }
}
