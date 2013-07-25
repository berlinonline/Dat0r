<?php

namespace Dat0r\Tests\CodeGen;

use Dat0r\Tests;
use Dat0r\CodeGen;
use Dat0r\CodeGen\Config;
use Dat0r\CodeGen\Parser;
use Symfony\Component\Filesystem;

class ServiceTest extends Tests\TestCase
{
    public function testBuildSchema()
    {
        $tmp_dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        $tmp_cache_path = $tmp_dir . 'testing_cache_' . mt_rand() . DIRECTORY_SEPARATOR;
        $tmp_deploy_path = $tmp_dir . 'testing_deploy_' . mt_rand() . DIRECTORY_SEPARATOR;

        $config = Config\Config::create(
            array(
                'cache_dir' => $tmp_cache_path,
                'deploy_dir' => $tmp_deploy_path,
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

        // @todo assert that stuff is correctly located inside the cache directory.

        $filesystem = new Filesystem\Filesystem();
        $filesystem->remove($tmp_cache_path);
        $filesystem->remove($tmp_deploy_path);
    }

    /* @todo implement
    public function testDeploySchema()
    {
    }
    */
}
