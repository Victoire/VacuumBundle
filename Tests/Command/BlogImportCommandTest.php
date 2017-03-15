<?php

namespace Victoire\DevTools\VacuumBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Victoire\DevTools\VacuumBundle\Command\BlogImportCommand;

class BlogImportCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $application->add(new BlogImportCommand());

        $command = $application->find('victoire:blog-import');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command' => $command->getName(),
            '--help' => ''
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('
        Usage:
            victoire:blog-import [options]
            
            Options:
              -b, --blog=BLOG          The name of the blog to populate
              -d, --dump=DUMP          Path to the dump who should bee imported
              -h, --help               Display this help message
              -q, --quiet              Do not output any message
              -V, --version            Display this application version
                  --ansi               Force ANSI output
                  --no-ansi            Disable ANSI output
              -n, --no-interaction     Do not ask any interactive question
              -s, --shell              Launch the shell.
                  --process-isolation  Launch commands from shell as a separate process.
              -e, --env=ENV            The Environment name. [default: "dev"]
                  --no-debug           Switches off debug mode.
              -new, --new[=NEW]        Force new blog generation
              -v|vv|vvv, --verbose     Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
            
            Help:
                  The victoire:blog-import command helps you to import blog contents from a dump.
              
                  Any passed option will be used as a default value for the interaction
                  
                  Required option
                  
                  -b --blog is required it expect a blog name
                  -d --dump is required it expect a path to the dump
                  
                  php app/console victoire:blog-import --blog=MyVictoireBlog --dump=/Path/To/My/Dump
                  
                  Other option
                  
                  -new --new will generate a new blog 
                  
                  If you want to disable any user interaction, use --no-interaction but don\'t forget to pass all needed options:
        ', $output);
    }
}