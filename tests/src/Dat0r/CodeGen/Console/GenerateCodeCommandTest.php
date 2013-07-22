<?php

namespace Dat0r\Tests\CodeGen\Console;

use Dat0r\Tests;
use Dat0r\CodeGen\Console as Dat0rConsole;
use Symfony\Component\Console as SymfonyConsole;
use Symfony\Component\Console\Tester;

class GenerateCodeCommandTest extends Tests\TestCase
{
    protected $application;

    protected $command;

    protected $fixtures_dir;

    public function setUp()
    {
        $this->application = new SymfonyConsole\Application();
        $this->application->add(new Dat0rConsole\GenerateCodeCommand());
        $this->command = $this->application->find(Dat0rConsole\GenerateCodeCommand::NAME);
        $this->fixtures_dir = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR;
    }
    public function testFoo()
    {
        $this->executeCommand(
            array(
                'action' => 'gen',
                '--config' => $this->fixtures_dir . 'schema_build.ini',
                '--schema' => $this->fixtures_dir . 'module_schema.xml'
            )
        );
    }

    protected function executeCommand(array $options = array())
    {
        $tester = new Tester\CommandTester($this->command);

        $tester->execute(
            array_merge(
                array('command' => $this->command->getName()),
                $options
            )
        );

        return $tester->getDisplay();
    }
}
