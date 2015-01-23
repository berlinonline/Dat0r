<?php

namespace Dat0r\Tests\CodeGen\Console;

use Mockery;
use Dat0r\Tests;
use Dat0r\CodeGen\Console as Dat0rConsole;
use Symfony\Component\Console as SymfonyConsole;
use Symfony\Component\Console\Tester;

class GenerateCodeCommandTest extends Tests\TestCase
{
    const FIXTURE_CONFIG = 'deploy_copy.ini';

    const FIXTURE_CONFIG_MOVE_DEPLOYMENT = 'deploy_move.ini';

    const FIXTURE_SCHEMA = 'type_schema.xml';

    protected $application;

    protected $command;

    protected $fixtures_dir;

    protected $service_mock;

    public function testValidConfigHandling()
    {
        $this->service_mock->shouldReceive('generate')->once();
        $this->service_mock->shouldReceive('deploy')->never();

        $this->command->setService($this->service_mock);

        $this->executeCommand(
            array(
                'action' => 'generate',
                '--config' => $this->fixtures_dir . self::FIXTURE_CONFIG,
                '--schema' => $this->fixtures_dir . self::FIXTURE_SCHEMA
            )
        );
    }

    public function testGenerateAction()
    {
        $this->service_mock->shouldReceive('generate')->once();
        $this->service_mock->shouldReceive('deploy')->never();

        $this->command->setService($this->service_mock);

        $this->executeCommand(
            array(
                'action' => 'generate',
                '--config' => $this->fixtures_dir . self::FIXTURE_CONFIG,
                '--schema' => $this->fixtures_dir . self::FIXTURE_SCHEMA
            )
        );
    }

    public function testDeployAction()
    {
        $this->service_mock->shouldReceive('generate')->once();
        $this->service_mock->shouldReceive('deploy')->once();

        $this->command->setService($this->service_mock);

        $this->executeCommand(
            array(
                'action' => 'generate+deploy',
                '--config' => $this->fixtures_dir . self::FIXTURE_CONFIG,
                '--schema' => $this->fixtures_dir . self::FIXTURE_SCHEMA
            )
        );
    }

    /**
     * @expectedException Dat0r\Common\Error\BadValueException
     */
    public function testInvalidAction()
    {
        $this->service_mock->shouldReceive('generate')->never();
        $this->service_mock->shouldReceive('deploy')->never();

        $this->command->setService($this->service_mock);

        $this->executeCommand(
            array(
                'action' => 'invalid_action',
                '--config' => $this->fixtures_dir . self::FIXTURE_CONFIG,
                '--schema' => $this->fixtures_dir . self::FIXTURE_SCHEMA
            )
        );
        // @codeCoverageIgnoreStart
    }   // @codeCoverageIgnoreEnd

    protected function setUp()
    {
        $this->fixtures_dir = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR;

        $this->application = new SymfonyConsole\Application();
        $this->application->add(new Dat0rConsole\GenerateCodeCommand());
        $this->command = $this->application->find('generate_code');

        $this->service_mock = Mockery::mock('Dat0r\\CodeGen\\Service', [ 'config' => [] ]);
    }

    protected function tearDown()
    {
        Mockery::close();
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
