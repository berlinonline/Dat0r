<?php

namespace Dat0r\Tests\CodeGen\Console;

use Dat0r\Tests;
use Dat0r\CodeGen\Console as Dat0rConsole;
use Symfony\Component\Console as SymfonyConsole;
use Symfony\Component\Console\Tester;

class GenerateCodeCommandTest extends Tests\TestCase
{
    public function testFoo()
    {
    }

    protected function executeCommand($filename, array $options = array())
    {
        $application = new SymfonyConsole\Application();
        $application->add(new Dat0rConsole\GenerateCodeCommand());

        $command = $application->find(Dat0rConsole\GenerateCodeCommand::NAME);
        $tester = new Tester\CommandTester($command);

        $tester->execute(
            array_merge(
                array(
                    'command' => $command->getName(),
                    '--config' => $this->getFixture($filename),
                ),
                $options
            )
        );

        return $tester->getDisplay();
    }
}
