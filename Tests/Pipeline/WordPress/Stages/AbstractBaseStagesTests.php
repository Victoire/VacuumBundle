<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Pipeline\WordPress\Stages;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
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
     * $params = [
            "dump",
            "blog_name",
            "blog_template",
            "blog_parent_id",
            "new_article_template",
            "article_template_name",
            "article_template_layout",
            "article_template_parent_id",
            "article_template_first_slot",
        ]; OR
     *  $params = [
            "dump",
            "blog_name",
            "blog_template",
            "blog_parent_id",
            "new_article_template",
            "article_template_id",
            "article_template_first_slot"
        ];
     * }
     * @param $xml
     * @return CommandPayload
     */
    public function getFreshPayload(array $params, $xml, Blog $blog = null)
    {
        $output = $this->createMock(OutputInterface::class);
        $output->method('getFormatter')->willReturn($this->createMock(OutputFormatterInterface::class));
        $questionHelper = $this->createMock(QuestionHelper::class);
        $rawData = simplexml_load_string($xml);
        $xmlHistory = $this->createMock(XMLHistoryManager::class);

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
