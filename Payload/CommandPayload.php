<?php

namespace Victoire\DevTools\VacuumBundle\Payload;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Victoire\Bundle\BlogBundle\Entity\Blog;
use Victoire\DevTools\VacuumBundle\Utils\History\XMLHistoryManager;

/**
 * Class WordPressPayload.
 */
class CommandPayload implements CommandPayloadInterface
{
    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var \SimpleXMLElement
     */
    private $rawData;

    /**
     * @var \Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog
     */
    private $tmpBlog;

    /**
     * @var Blog
     */
    private $newVicBlog;

    /**
     * @var XMLHistoryManager
     */
    private $XMLHistoryManager;

    /**
     * CommandPayload constructor.
     *
     * @param array             $parameters
     * @param OutputInterface   $output
     * @param QuestionHelper    $questionHelper
     * @param \SimpleXMLElement $rawData
     * @param XMLHistoryManager $XMLHistoryManager
     */
    public function __construct(
        array $parameters,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        \SimpleXMLElement $rawData,
        XMLHistoryManager $XMLHistoryManager
    ) {
        $this->parameters = $parameters;
        $this->questionHelper = $questionHelper;
        $this->output = $output;
        $this->rawData = $rawData;
        $this->XMLHistoryManager = $XMLHistoryManager;
        self::loadCustomStyle();
    }

    /**
     * Generate custom style for command dispatch.
     */
    private function loadCustomStyle()
    {
        $style = new OutputFormatterStyle('white', 'blue');
        $this->output->getFormatter()->setStyle('stageTitle', $style);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getParameter($key)
    {
        return $this->parameters[$key];
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function addParameter($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     *
     * @return CommandPayload
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     *
     * @return CommandPayload
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @return QuestionHelper
     */
    public function getQuestionHelper()
    {
        return $this->questionHelper;
    }

    /**
     * @param QuestionHelper $questionHelper
     *
     * @return CommandPayload
     */
    public function setQuestionHelper(QuestionHelper $questionHelper)
    {
        $this->questionHelper = $questionHelper;

        return $this;
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param \SimpleXMLElement $rawData
     *
     * @return CommandPayload
     */
    public function setRawData(\SimpleXMLElement $rawData)
    {
        $this->rawData = $rawData;

        return $this;
    }

    /**
     * @return \Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog
     */
    public function getTmpBlog()
    {
        return $this->tmpBlog;
    }

    /**
     * @param \Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog $tmpBlog
     *
     * @return CommandPayload
     */
    public function setTmpBlog(\Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog $tmpBlog)
    {
        $this->tmpBlog = $tmpBlog;

        return $this;
    }

    /**
     * @return Blog
     */
    public function getNewVicBlog()
    {
        return $this->newVicBlog;
    }

    /**
     * @param Blog $newVicBlog
     *
     * @return CommandPayload
     */
    public function setNewVicBlog(Blog $newVicBlog)
    {
        $this->newVicBlog = $newVicBlog;

        return $this;
    }

    /**
     * @return XMLHistoryManager
     */
    public function getXMLHistoryManager()
    {
        return $this->XMLHistoryManager;
    }

    /**
     * @param XMLHistoryManager $XMLHistoryManager
     *
     * @return CommandPayload
     */
    public function setXMLHistoryManager(XMLHistoryManager $XMLHistoryManager)
    {
        $this->XMLHistoryManager = $XMLHistoryManager;

        return $this;
    }

    /**
     * @param null $value
     *
     * @return ProgressBar
     */
    public function getNewProgressBar($value = null)
    {
        if (null == $value) {
            return new ProgressBar($this->output);
        }

        return new ProgressBar($this->output, $value);
    }

    /**
     * @param $message
     */
    public function getNewStageTitleMessage($message)
    {
        $this->output->writeln('<stageTitle>'.$message.'</stageTitle>');
    }

    /**
     * @param $message
     */
    public function getNewSuccessMessage($message)
    {
        $this->output->writeln('<info>'.$message.'</info>');
    }

    public function jumpLine()
    {
        $this->output->writeln('');
    }

    /**
     * @param $message
     */
    public function throwErrorAndStop($message)
    {
        $this->output->writeln('<error>'.$message.'</error>');
        exit(1);
    }
}
