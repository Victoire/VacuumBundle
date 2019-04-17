<?php

namespace Victoire\DevTools\VacuumBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class BlogImportCommand.
 */
class BlogImportCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('victoire:blog-import')
            ->setDefinition([
                new InputOption('blog-name', '-b', InputOption::VALUE_REQUIRED, 'The name of the blog to populate'),
                new InputOption('blog-template', '-bt', InputOption::VALUE_REQUIRED, 'The id of the blog template'),
                new InputOption('blog-parent-id', '-bpi', InputOption::VALUE_REQUIRED, 'The id of the blog parent page'),
                new InputOption('dump', '-d', InputOption::VALUE_REQUIRED, 'Path to the dump who should bee imported'),
                new InputOption('article-template-name', '-atn', InputOption::VALUE_OPTIONAL, 'article template name'),
                new InputOption('article-template-layout', '-atl', InputOption::VALUE_OPTIONAL, 'article template layout designation'),
                new InputOption('article-template-parent-id', '-atpid', InputOption::VALUE_OPTIONAL, 'article template parent id'),
                new InputOption('article-template-id', '-ati', InputOption::VALUE_OPTIONAL, 'Id of an existing article template'),
                new InputOption('article-template-first-slot', '-atfs', InputOption::VALUE_OPTIONAL, 'slot designation for root widget map in article template'),
            ])
            ->setDescription('Import blog form dump')
            ->setHelp(<<<'EOT'
    The <info>victoire:blog-import</info> command helps you to import blog contents from a dump.

    Any passed option will be used as a default value for the interaction
    
    <info>Required option</info>
    
    <comment>-b --blog</comment> is required it expect a blog name
    <comment>-d --dump</comment> is required it expect a path to the dump
    <comment>-bt --blog-template</comment> is required it expect an id for base blog template
    <comment>-bpi --blog-parent-id</comment> is required it expect an id for blog parent page
    
    <info>If you choose to let the bundle create a new ArticleTemplate</info>
    <comment>-atn --article-template-name</comment> is required a name for the new ArticleTemplate
    <comment>-atl --article-template-layout</comment> is required a layout designation for the new ArticleTemplate
    <comment>-atfs --article-template-first-slot</comment> is required a slot designation where ArticleContent Widget will be attached
    <comment>-atpid --article-template-parent-id</comment> is required an base Template id for the new ArticleTemplate
    
    <info>If you choose to use an existing ArticleTemplate</info>
    <comment>-ati --atricle-template-id</comment> is required the ArticleTemplate id
    
    <info>php app/console victoire:blog-import --blog=MyVictoireBlog --dump=/Path/To/My/Dump</info>
    
    <info>Other option</info>
    If you want to disable any user interaction, use <comment>--no-interaction</comment> but don't forget to pass all needed options:
EOT
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $commandParameters = [];

        $requiredParameter = ['blog-name', 'dump', 'article-template-first-slot'];

        foreach ($requiredParameter as $parameter) {
            if (null == $input->getOption($parameter)) {
                throw new \RuntimeException(sprintf('The "%s" parameter must be provided', $parameter));
            }
        }

        // Xml dump path
        $commandParameters['dump'] = $input->getOption('dump');

        // Blog name
        $commandParameters['blog_name'] = $input->getOption('blog-name');

        // Blog template id
        $commandParameters['blog_template'] = $input->getOption('blog-template');

        // Blog parent id
        $commandParameters['blog_parent_id'] = $input->getOption('blog-parent-id');

        // Article Template
        if (null !== $input->getOption('article-template-id')) {
            $commandParameters['new_article_template'] = false;
            $commandParameters['article_template_id'] = $input->getOption('article-template-id');
            $commandParameters['article_template_first_slot'] = $input->getOption('article-template-first-slot');
        } else {
            $commandParameters['new_article_template'] = true;
            $commandParameters['article_template_name'] = $input->getOption('article-template-name');
            $commandParameters['article_template_layout'] = $input->getOption('article-template-layout');
            $commandParameters['article_template_parent_id'] = $input->getOption('article-template-parent-id');
            $commandParameters['article_template_first_slot'] = $input->getOption('article-template-first-slot');
        }

        if ($input->isInteractive()) {
            $message = '';
            foreach ($commandParameters as $key => $parameter) {
                $message .= '<info>'.$key.':</info> '.$parameter."\n";
            }

            //summary
            $output->writeln([
                '',
                $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
                '',
                sprintf(
                    "<error>Do you confirm blog import with following parameters:</error> \n %s",
                    $message
                ),
                '',
            ]);

            $question = new ConfirmationQuestion(
                $questionHelper->getQuestion(
                    'do you wish to continue ?', 'yes', '?'),
                    true
            );

            if (!$questionHelper->ask($input, $output, $question)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $ioWordPressPipeline = $this->getContainer()->get('victoire.vacuum_bundle.io_word_press.pipeline');
        $ioWordPressPipeline->preparePipeline($commandParameters, $output, $questionHelper);
    }

    /**
     * @param InputInterface  $input
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
        $question = new Question($questionHelper->getQuestion('blog name', $input->getOption('blog-name')));

        $blogName = (string) $questionHelper->ask($input, $output, $question);
        $input->setOption('blog-name', $blogName);

        // blog template id
        $question = new Question($questionHelper->getQuestion('blog template id', $input->getOption('blog-template')));
        $question->setValidator(function ($answer) {
            return self::validateTemplateId($answer);
        });

        $blogTemplateId = (int) $questionHelper->ask($input, $output, $question);
        $input->setOption('blog-template', $blogTemplateId);

        // blog parent id
        $question = new Question($questionHelper->getQuestion('blog parent id', $input->getOption('blog-parent-id')));
        $question->setValidator(function ($answer) {
            return self::validateView($answer);
        });

        $blogTemplateId = (int) $questionHelper->ask($input, $output, $question);
        $input->setOption('blog-parent-id', $blogTemplateId);

        // path to dump
        $question = new Question($questionHelper->getQuestion('path to dump', $input->getOption('dump')));
        $question->setValidator(function ($answer) {
            return self::validatePath($answer);
        });

        $pathToDump = (string) $questionHelper->ask($input, $output, $question);
        $input->setOption('dump', $pathToDump);

        // Use existing ArticleTemplate
        $question = new ConfirmationQuestion('Use Existing Article Template(yes/no): ', false);

        if (!$questionHelper->ask($input, $output, $question)) {

            // ArticleTemplate name
            $question = new Question($questionHelper->getQuestion('Article template name', $input->getOption('article-template-name')));
            $articleTemplateName = (string) $questionHelper->ask($input, $output, $question);
            $input->setOption('article-template-name', $articleTemplateName);

            // ArticleTemplate layout
            $question = new Question($questionHelper->getQuestion('Article template layout', $input->getOption('article-template-layout')));
            $question->setValidator(function ($answer) {
                return self::validateLayout($answer);
            });
            $articleTemplateLayout = (string) $questionHelper->ask($input, $output, $question);
            $input->setOption('article-template-layout', $articleTemplateLayout);

            // ArticleTemplate parent_id
            $question = new Question($questionHelper->getQuestion('Article template parent id', $input->getOption('article-template-parent-id')));
            $question->setValidator(function ($answer) {
                return self::validateTemplateId($answer);
            });

            $articleTemplateParentId = (int) $questionHelper->ask($input, $output, $question);
            $input->setOption('article-template-parent-id', $articleTemplateParentId);

            // ArticleTemplate slot
            $question = new Question($questionHelper->getQuestion('Article Template first slot', $input->getOption('article-template-first-slot')));
            $articleTemplateFirstSlot = (string) $questionHelper->ask($input, $output, $question);
            $input->setOption('article-template-first-slot', $articleTemplateFirstSlot);
        } else {

            // ArticleTemplate Id
            $question = new Question($questionHelper->getQuestion('Article Template id', $input->getOption('article-template-id')));
            $question->setValidator(function ($answer) {
                return self::validateArticleTemplateId($answer);
            });

            $articleTemplateId = (int) $questionHelper->ask($input, $output, $question);
            $input->setOption('article-template-id', $articleTemplateId);

            // ArticleTemplate slot
            $question = new Question($questionHelper->getQuestion('Article Template first slot', $input->getOption('article-template-first-slot')));
            $articleTemplateFirstSlot = (string) $questionHelper->ask($input, $output, $question);
            $input->setOption('article-template-first-slot', $articleTemplateFirstSlot);
        }
    }

    /**
     * @param $id int
     *
     * @return int
     */
    private function validateView($id)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $result = $em->getRepository('Victoire\Bundle\CoreBundle\Entity\View')->find($id);
        if (empty($result)) {
            throw new \RuntimeException('Can\'t find any vic view with id: "%s"', $id);
        }

        return $id;
    }

    /**
     * @param $path
     */
    private function validatePath($path)
    {
        if (!realpath($path)) {
            throw new \RuntimeException(sprintf('Wrong path the file "%s" can\'t be found', $path));
        }

        if (mime_content_type($path) != 'application/xml') {
            throw new \RuntimeException('Wrong file format. Format accepted "xml"');
        }

        return $path;
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
     *
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

        return $name;
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
