<?php

namespace Dat0r\Tests\CodeGen;

use Dat0r\Tests;
use Dat0r\CodeGen;
use Dat0r\CodeGen\Config;
use Dat0r\CodeGen\Parser;

class ServiceTest extends Tests\TestCase
{
    public function testBuildSchema()
    {
        $config = Config\Config::create(
            array(
                'cache_dir' => sys_get_temp_dir() . '/dat0r_tmp/.code_cache',
                'deploy_dir' => sys_get_temp_dir() . '/dat0r_tmp/.code_cache',
                'plugin_settings' => array()
            )
        );

        $module_schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'simple_schema_with_aggregate.xml';

        $codegen_service = CodeGen\Service::create(
            array(
                'config' => $config,
                'schema_parser' => Parser\ModuleSchemaXmlParser::create()
            )
        );

        $codegen_service->buildSchema($module_schema_path);
    }
}
