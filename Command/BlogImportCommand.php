<?php

namespace Victoire\DevTools\VacuumBundle\Command;

use Buzz\Exception\RuntimeException;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Victoire\DevTools\VacuumBundle\Pipeline\WordPress\IOWordPressPipeline;
use Symfony\Component\Console\Question\Question;

/**
 * Class BlogImportCommand
 * @package Victoire\VacuumBundle\Command
 */
class BlogImportCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('victoire:blog-import')
            ->setDefinition([
                new InputOption('blog_name', '-b', InputOption::VALUE_OPTIONAL, 'The name of the blog to populate'),
                new InputOption('dump', '-d', InputOption::VALUE_OPTIONAL, 'Path to the dump who should bee imported'),
                new InputOption('new_article_template', '-nat', InputOption::VALUE_OPTIONAL, 'Define if you want to use an existing article template or create one'),
                new InputOption('article_template_name', '-atn', InputOption::VALUE_OPTIONAL, 'article template name'),
                new InputOption('article_template_layout', '-atl', InputOption::VALUE_OPTIONAL, 'article template layout designation'),
                new InputOption('article_template_parent_id', '-atpid', InputOption::VALUE_OPTIONAL, 'article template parent id'),
                new InputOption('article_template_id', '-ati', InputOption::VALUE_OPTIONAL, 'Id of an existing article template'),
                new InputOption('article_template_first_slot', '-atfs', InputOption::VALUE_OPTIONAL, 'slot designation for root widget map in article template')
            ])
            ->setDescription('Import blog form dump')
            ->setHelp(<<<'EOT'
    The <info>victoire:blog-import</info> command helps you to import blog contents from a dump.

    Any passed option will be used as a default value for the interaction
    
    <info>Required option</info>
    
    <comment>-b --blog</comment> is required it expect a blog name
    <comment>-d --dump</comment> is required it expect a path to the dump
    
    <info>php app/console victoire:blog-import --blog=MyVictoireBlog --dump=/Path/To/My/Dump</info>
    
    <info>Other option</info>
    
    <comment>-new --new</comment> will generate a new blog 
    
    If you want to disable any user interaction, use <comment>--no-interaction</comment> but don't forget to pass all needed options:
EOT
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();

        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion($questionHelper->getQuestion('Do you confirm blog import', 'yes', '?'), true);
            if (!$questionHelper->ask($input, $output, $question)) {
                $output->writeln('<error>Command aborted</error>');
                return 1;
            }
        }

        $requiredParameter = ["blog_name", "dump", "new_article_template", "article_template_first_slot"];

        foreach ($requiredParameter as $parameter) {
            if (null === $input->getOption($parameter)) {
                throw new \RuntimeException(sprintf('The "%s" parameter must be provided', $parameter));
            }
        }

        $pathToDump = $input->getOption('dump');

        if (!realpath($pathToDump)) {
            throw new RuntimeException(sprintf('Wrong path the file "%s" can\'t be found', $pathToDump));
        }

        if (true == $input->getOption("new_article_template")) {

            $requiredParameter = ["article_template_name", "article_template_layout", "article_template_parent_id"];

            foreach ($requiredParameter as $parameter) {
                if (null === $input->getOption($parameter)) {
                    throw new \RuntimeException(sprintf('The "%s" parameter must be provided', $parameter));
                }
            }
        }

        $ioWordPressPipeline = $this->getContainer()->get('victoire.vacuum_bundle.io_word_press.pipeline');
        $ioWordPressPipeline->process($pathToDump);
        $output = $ioWordPressPipeline->getOutput();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the Victoire blog importer');

        ///////////////////////
        //                   //
        //    Create Blog    //
        //                   //
        ///////////////////////

        // blog name
        $question = new Question($questionHelper->getQuestion('blog name', $input->getOption('blog_name')));
        $question->setValidator(function ($answer) {
           return self::validateBlogName($answer);
        });

        $questionHelper->ask($input, $output, $question);

        // path to dump
        $question = new Question($questionHelper->getQuestion('path to dump', $input->getOption('dump')));
        $question->setValidator(function ($answer) {
           return self::validatePath($answer);
        });

        $questionHelper->ask($input, $output, $question);

        // Use existing ArticleTemplate
        $question = new ConfirmationQuestion('Use Existing Article Template: ', false);

        if (!$questionHelper->ask($input, $output, $question)) {

            // ArticleTemplate name
            $question = new Question($questionHelper->getQuestion('Article template name', $input->getOption('article_template_name')));
            $questionHelper->ask($input, $output, $question);

            // ArticleTemplate layout
            $question = new Question($questionHelper->getQuestion('Article template layout', $input->getOption('article_template_layout')));
            $question->setValidator(function ($answer) {
                return self::validateLayout($answer);
            });
            $questionHelper->ask($input, $output, $question);

            // ArticleTemplate parent_id
            $question = new Question($questionHelper->getQuestion('Article template parent id', $input->getOption('article_template_parent_id')));
            $question->setValidator(function ($answer) {
               return self::validateTemplateId($answer);
            });

            $questionHelper->ask($input, $output, $question);

            // ArticleTemplate slot
            $question = new Question($questionHelper->getQuestion('Article Template first slot', $input->getOption('article_template_first_slot')));
            $questionHelper->ask($input, $output, $question);

        } else {

            // ArticleTemplate Id
            $question = new Question($questionHelper->getQuestion('Article Template id', $input->getOption('article_template_id')));
            $question->setValidator(function ($answer) {
                return self::validateArticleTemplateId($answer);
            });

            $questionHelper->ask($input, $output, $question);

            // ArticleTemplate slot
            $question = new Question($questionHelper->getQuestion('Article Template first slot', $input->getOption('article_template_first_slot')));
            $questionHelper->ask($input, $output, $question);
        }
    }

    /**
     * @param $name
     */
    private function validateBlogName($name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $results = $em->getRepository('Victoire\Bundle\BlogBundle\Entity\Blog')->findAll();
        foreach ($results as $result) {
            if ($result->getName() == $name) {
                throw new \RuntimeException(sprintf('Blog with name "%s" already exist', $name));
            }
        }
    }

    /**
     * @param $path
     */
    private function validatePath($path)
    {
        if (!realpath($path)) {
            throw new RuntimeException(sprintf('Wrong path the file "%s" can\'t be found', $path));
        }

        if (mime_content_type($path) != "application/xml") {
            throw new RuntimeException('Wrong file format. Format accepted "xml"');
        }
    }

    /**
     * @param $id
     */
    private function validateTemplateId($id)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        if (null == $em->getRepository('Victoire\Bundle\TemplateBundle\Entity\Template')->find($id)) {
            throw new \RuntimeException(sprintf('can\'t found template with id "%s"', $id));
        }
    }

    /**
     * @param $id
     */
    private function validateArticleTemplateId($id)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        if (null == $em->getRepository('Victoire\Bundle\BlogBundle\Entity\ArticleTemplate')->find($id)) {
            throw new \RuntimeException(sprintf('can\'t found ArticleTemplate with id "%s"', $id));
        }
    }

    /**
     * @param $name
     * @return ConfirmationQuestion
     */
    private function validateLayout($name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $views = $em->getRepository('Victoire\Bundle\CoreBundle\Entity\View')->findAll();
        $use = 0;

        foreach ($views as $view) {
            if (method_exists($view, 'getLayout')) {
                if ($name == $view->getLayout()) {
                    $use++;
                }
            }
        }

        if ($use == 0) {
            throw new \RuntimeException(sprintf(
                'There is no mention of layout "%s" in any view. Please check your typo or if layout exist.',
                $name
            ));
        }
    }

    /**
     * @return QuestionHelper|\Symfony\Component\Console\Helper\HelperInterface
     */
    protected function getQuestionHelper()
    {
        $questionHelper = $this->getHelperSet()->get('question');
        if (!$questionHelper || get_class($questionHelper) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper') {
            $this->getHelperSet()->set($questionHelper = new QuestionHelper());
        }

        return $questionHelper;
    }
}