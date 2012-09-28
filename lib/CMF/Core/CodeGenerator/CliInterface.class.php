<?php

namespace CMF\Core\CodeGenerator;

class CliInterface
{
    const ACTION_GENERATE = 'gen';

    const ACTION_DEPLOY = 'dep';

    public static function run()
    {
        try
        {
            $options = CliOptionsProvider::getOptions();
            $configuration = Configuration::create(parse_ini_file($options['config']));
            $parser = ModuleDefinitionParser::create();
            $moduleDefinition = $parser->parse($options['module_definition']);
            $actions = $options['actions'];

            if (in_array(self::ACTION_GENERATE, $actions))
            {
                $builder = Builder::create($configuration);
                $builder->build($moduleDefinition);
            }
            if (in_array(self::ACTION_DEPLOY, $actions))
            {
                $deployment = Deployment::create($configuration);
                $deployment->deploy($moduleDefinition);
            }
        }
        catch (\Exception $e)
        {
            echo "Something went wrong while trying to generate code." . PHP_EOL;
            echo "Error: " . $e->getMessage() . PHP_EOL;
            if ($e instanceof OptionParseException)
            {
                echo <<<usage
Usage: php gen.php -a {action} -c {config} -d {module_definition}
- {action} must be one of dep, gen or dep+gen and defines the action to execute.
- {module_definition} Must point to a module definition file.
- {config} Must point to a config (ini)file.

usage;
            }
        }
    }
}
