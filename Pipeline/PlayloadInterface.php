<?php

namespace Victoire\DevTools\VacuumBundle\Pipeline;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface PlayloadInterface
 * @package Victoire\DevTools\VacuumBundle\Pipeline
 */
interface PlayloadInterface
{
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
    );

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @param $key string
     * @return mixed
     */
    public function getParameter($key);

    /**
     * @return PlayloadInterface
     */
    public function setParameters(array $parameters);

    /**
     * @return ProgressBar
     */
    public function getProgressBar($value);

    /**
     * @return QuestionHelper
     */
    public function getQuestionHelper();

    /**
     * @return PlayloadInterface
     */
    public function setQuestionHelper(QuestionHelper $questionHelper);

    /**
     * @return \SimpleXMLElement
     */
    public function getRawData();

    /**
     * @return PlayloadInterface
     */
    public function setRawData(\SimpleXMLElement $rawData);

    /**
     * @return OutputInterface
     */
    public function getOutput();
}