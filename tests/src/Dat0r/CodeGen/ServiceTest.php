<?php

namespace Dat0r\Tests\CodeGen;

use Dat0r\Tests;
use Dat0r\CodeGen;

class ServiceTest extends Tests\TestCase
{
    public function testBuildSchema()
    {
        $config = CodeGen\Config::create(
            array(
                'cache_dir' => __DIR__ . DIRECTORY_SEPARATOR . '.code_cache',
                'deploy_dir' => '',
                'plugin_settings' => array()
            )
        );

        $module_schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'simple_schema_with_aggregate.xml';

        $codegen_service = new CodeGen\Service($config);
        $codegen_service->buildSchema($module_schema_path);
    }
}
