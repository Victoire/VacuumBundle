<?php

namespace Victoire\DevTools\VacuumBundle\Playload;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Victoire\Bundle\BlogBundle\Entity\Blog;
use Victoire\Bundle\MediaBundle\Entity\Folder;
use Victoire\Bundle\WidgetMapBundle\Entity\WidgetMap;

/**
 * Class WordPressPlayload
 * @package Victoire\DevTools\VacuumBundle\Pipeline\WordPress
 */
class CommandPlayload implements CommandPlayloadInterface
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
     * WordPressPlayload constructor.
     * @param array $parameters
     * @param ProgressBar $progressBar
     * @param QuestionHelper $questionHelper
     */
    public function __construct(
        array $parameters,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        \SimpleXMLElement $rawData
    )
    {
        $this->parameters = $parameters;
        $this->questionHelper = $questionHelper;
        $this->output = $output;
        $this->rawData = $rawData;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getParameter($key)
    {
        return $this->parameters[$key];
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
     * @return CommandPlayload
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
     * @return CommandPlayload
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
     * @return CommandPlayload
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
     * @return CommandPlayload
     */
    public function setRawData(\SimpleXMLElement $rawData)
    {
        $this->rawData = $rawData;
        return $this;
    }

    /**
     * @return \Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog
     */
    public function getTmpBlog(): \Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog
    {
        return $this->tmpBlog;
    }

    /**
     * @param \Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog $tmpBlog
     * @return CommandPlayload
     */
    public function setTmpBlog(\Victoire\DevTools\VacuumBundle\Entity\WordPress\Blog $tmpBlog): CommandPlayload
    {
        $this->tmpBlog = $tmpBlog;
        return $this;
    }

    /**
     * @return Blog
     */
    public function getNewVicBlog(): Blog
    {
        return $this->newVicBlog;
    }

    /**
     * @param Blog $newVicBlog
     * @return CommandPlayload
     */
    public function setNewVicBlog(Blog $newVicBlog): CommandPlayload
    {
        $this->newVicBlog = $newVicBlog;
        return $this;
    }

    /**
     * @param null $value
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
    public function getNewSuccessMessage($message)
    {
        $this->output->writeln("<info>".$message."</info>");
    }

    /**
     * @param $message
     */
    public function throwErrorAndStop($message)
    {
        $this->output->writeln("<error>".$message."</error>");
        exit(1);
    }
}