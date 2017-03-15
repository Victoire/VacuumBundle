<?php

namespace Victoire\DevTools\VacuumBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
                new InputOption('blog', '-b', InputOption::VALUE_REQUIRED, 'The name of the blog to populate'),
                new InputOption('dump', '-d', InputOption::VALUE_REQUIRED, 'Path to the dump who should bee imported'),
                new InputOption('new', '-n', InputOption::VALUE_OPTIONAL, 'Force new blog generation')
            ])
            ->setDescription('Import blog form dump')
            ->setHelp(<<<'EOT'
    The <info>victoire:blog-import</info> command helps you to import blog contents from a dump.

    Any passed option will be used as a default value for the interaction
    
    <info>Required option</info>
    
    <comment>--blog</comment> is required it expect a blog name
    <comment>--dump</comment> is required it expect a path to the dump
    
    <info>php app/console victoire:blog-import --blog=MyVictoireBlog --dump=/Path/To/My/Dump</info>
    
    <info>Other option</info>
    
    <comment>--new</comment> will generate a new blog 
    
    If you want to disable any user interaction, use <comment>--no-interaction</comment> but don't forget to pass all needed options:
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}