<?php

/**
 * Quick and dirty autoload hack to get the try_me.php up and running for playing around.
 * As we'll be using agavi's autoloading later on and loading by (namespace)convention wont work for some classes
 * we doing it manually for now.
 * Just close this file and pretend you've never seen it.
 */

require_once dirname(dirname(dirname(__FILE__))) . '/lib/CMF/Core/Runtime/Autoloader.class.php';
CMF\Core\Runtime\Autoloader::register();

// DOMAIN STUFF
$domainRuntime = array(
    'Shofi/Place/base' => array(
        'BasePlaceModule', 'BasePlaceDocument', 'BaseCoreItemModule', 'BaseCoreItemDocument', 
        'BaseLocationModule', 'BaseLocationDocument'
    ),
    'Shofi/Place' => array(
        'PlaceModule', 'PlaceDocument', 'CoreItemModule', 'CoreItemDocument', 
        'LocationModule', 'LocationDocument'
    ),
    'Foo/base' => array('BaseFooModule', 'BaseFooDocument', 'BaseBarModule', 'BaseBarDocument'),
    'Foo' => array('FooModule', 'FooDocument', 'BarModule', 'BarDocument'),
);

$autoload = function($baseDirectory, array $packages)
{
    foreach ($packages as $package => $classes)
    {
        foreach ($classes as $class)
        {
            $classFilepath = $baseDirectory . $package . '/' . $class . '.class.php';
            $interfaceFilepath = $baseDirectory . $package . '/' . $class . '.iface.php';
            if (file_exists($classFilepath))
            {
                require_once $classFilepath;
            }
            elseif (file_exists($interfaceFilepath))
            {
                require_once $interfaceFilepath;
            }
            else
            {
                throw new Exception(
                    "Failed to autoload class: " . $class . ", tryed paths: " . $classFilepath . " and " . $interfaceFilepath
                );
            }
        }
    }
};

$autoload(dirname(__FILE__) . '/modules/', $domainRuntime);
