<?php

namespace Dat0r\Tests\CodeGen\Console;

use Dat0r\Tests;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateCommandCommandTest extends Tests\TestCase
{
    public function testAutoloadDirOption()
    {
        $text = $this->executeCheckCommand('empty.json', array(
            '--autoload_dir' => __DIR__,
            '--verbose' => true
        ));

        $this->assertRegExp('/Classes will be autoloaded from "' . preg_quote(__DIR__, '/') . '"/', $text);
    }

    /**
     * Executes CheckCommand with given config file and options.
     *
     * @param string $filename name of file in Fixtures folder
     * @param array $options CLI options
     *
     * @return string output
     */
    protected function executeCheckCommand($filename, array $options = array())
    {
        $application = new Application();
        $application->add(new Console\GenerateCodeCommand());

        $command = $application->find(Console\GenerateCodeCommand::NAME);
        $tester = new CommandTester($command);

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
