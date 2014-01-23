<?php

namespace Dat0r\Tests\CodeGen;

use Dat0r\Tests;
use Dat0r\CodeGen\Service;
use Dat0r\CodeGen\Config;
use Dat0r\CodeGen\Parser\ModuleSchema\ModuleSchemaXmlParser;
use Symfony\Component\Filesystem;

class ServiceTest extends Tests\TestCase
{
    protected $config;

    protected $schema_path;

    public function testBuildSchema()
    {
        $codegen_service = Service::create(
            array(
                'config' => $this->config,
                'schema_parser' => ModuleSchemaXmlParser::create()
            )
        );

        $codegen_service->buildSchema($this->schema_path);
        // @todo assert validity of the generated code inside the configured cache directory.
    }

    public function testDeployMethodMove()
    {
        $this->config->setDeployMethod('move');

        $codegen_service = Service::create(
            array(
                'config' => $this->config,
                'schema_parser' => ModuleSchemaXmlParser::create()
            )
        );

        $codegen_service->buildSchema($this->schema_path);
        $codegen_service->deployBuild();
        // @todo assert validity of the generated code inside the configured deploy directory.
    }

    public function testDeployMethodCopy()
    {
        $this->config->setDeployMethod('copy');

        $codegen_service = Service::create(
            array(
                'config' => $this->config,
                'schema_parser' => ModuleSchemaXmlParser::create()
            )
        );

        $codegen_service->buildSchema($this->schema_path);
        $codegen_service->deployBuild();
        // @todo assert validity of the generated code inside the configured deploy directory.
    }

    protected function setUp()
    {
        $tmp_dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        $tmp_cache_path = $tmp_dir . 'testing_cache_' . mt_rand() . DIRECTORY_SEPARATOR;
        $tmp_deploy_path = $tmp_dir . 'testing_deploy_' . mt_rand() . DIRECTORY_SEPARATOR;

        $this->config = Config::create(
            array(
                'cache_dir' => $tmp_cache_path,
                'deploy_dir' => $tmp_deploy_path,
                'plugin_settings' => array()
            )
        );

        $this->schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'simple_schema_with_aggregate.xml';
    }

    protected function tearDown()
    {
       /* $filesystem = new Filesystem\Filesystem();
        $filesystem->remove($this->config->getCacheDir());
        $filesystem->remove($this->config->getDeployDir()); */
    }
}
