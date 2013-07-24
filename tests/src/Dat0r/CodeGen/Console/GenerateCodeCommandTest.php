<?php

namespace Dat0r\Tests\CodeGen\Console;

use Dat0r\Tests;
use Dat0r\CodeGen\Console as Dat0rConsole;
use Symfony\Component\Console as SymfonyConsole;
use Symfony\Component\Console\Tester;

class GenerateCodeCommandTest extends Tests\TestCase
{
    const FIXTURE_CONFIG = 'deploy_copy.ini';

    const FIXTURE_CONFIG_MOVE_DEPLOYMENT = 'deploy_move.ini';

    const FIXTURE_SCHEMA = 'module_schema.xml';

    protected $application;

    protected $command;

    protected $fixtures_dir;

    public function setUp()
    {
        $this->application = new SymfonyConsole\Application();
        $this->application->add(new Dat0rConsole\GenerateCodeCommand());
        $this->command = $this->application->find(Dat0rConsole\GenerateCodeCommand::NAME);
        $this->fixtures_dir = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR;

        // reset testing-cache and -deploy directories
        // @see Fixtures/deploy_copy.ini and Fixtures/deploy_move.ini
        `rm -rf /tmp/dat0r_cache_test_dir`;
        `rm -rf /tmp/dat0r_deploy_test_dir`;
    }

    public function testGenerateAction()
    {
        $this->executeCommand(
            array(
                'action' => 'generate',
                '--config' => $this->fixtures_dir . self::FIXTURE_CONFIG,
                '--schema' => $this->fixtures_dir . self::FIXTURE_SCHEMA
            )
        );

        // @todo assert /tmp/dat0r_cache_test_dir contents
    }

    public function testDeployCopyAction()
    {
        $this->executeCommand(
            array(
                'action' => 'generate+deploy',
                '--config' => $this->fixtures_dir . self::FIXTURE_CONFIG,
                '--schema' => $this->fixtures_dir . self::FIXTURE_SCHEMA
            )
        );

        // @todo assert /tmp/dat0r_deploy_test_dir contents
        // and that the cache dir is still there.
    }

    public function testDeployMoveAction()
    {
        $this->executeCommand(
            array(
                'action' => 'generate+deploy',
                '--config' => $this->fixtures_dir . self::FIXTURE_CONFIG_MOVE_DEPLOYMENT,
                '--schema' => $this->fixtures_dir . self::FIXTURE_SCHEMA
            )
        );

        // @todo assert /tmp/dat0r_deploy_test_dir contents
        // and that the cache dir is gone.
    }

    /**
     * @expectedException Dat0r\CodeGen\Console\Exception
     */
    public function testInvalidAction()
    {
        $this->executeCommand(
            array(
                'action' => 'invalid_action',
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
