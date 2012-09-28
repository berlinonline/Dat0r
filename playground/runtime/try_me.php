<?php

require_once dirname(__FILE__) . '/autoload.php';

use CMF\Runtime\Domain\Shofi\Place;
use CMF\Runtime\Domain\Foo;
use CMF\Core\Runtime\Document;
use CMF\Core\Runtime\ValueHolder;

// Static helper class for resursively printing ValueChangedEvents.
class EventOutput
{
    public static function printEvent(Document\ValueChangedEvent $event, $rootModule, Document\DocumentChangedEvent $parent = NULL)
    {
        printf("value changed for %s-field '%s::%s' detected at t-%s:\n", 
            $parent ? 'aggregate' : 'root',
            $parent ? $parent->getDocument()->getModule()->getName() : $rootModule,
            $event->getField()->getName(), 
            $event->getTimestamp()
        );

        if (($origin = $event->getAggregateEvent()))
        {
            self::printEvent($origin->getValueChangedEvent(), $rootModule, $origin);
        }
        else
        {
            printf("old value: %s\nnew value: %s\n--------------------------\n", 
                $event->getOldValue(), 
                $event->getNewValue()
            );
        }
    }
}

// API example for creating a document and changing some values.
$module = Foo\FooModule::getInstance();
$foo = $module->createDocument(array(
    'title' => 'This is an foo title.',
    'description' => 'This is an foo description.',
    'bar' => array(
        'food' => 'This is a bars food data.',
        'clickCount' => '23'
    )
));

$foo->getBar()->setFood("This is the food-data after it was changed");
$foo->getBar()->setClickCount(46);
$foo->setDescription("This is the description after it was changed");

// Small debug loop for checking change events.
foreach ($foo->getChanges() as $changeEvent)
{
    EventOutput::printEvent($changeEvent, $module->getName());
}

var_dump($foo->toArray());

/*
$placeModule = Place\PlaceModule::getInstance();
// Memory penetration loop to see, if php's gc is still friends with our class layout.
echo "Lets go: " . round(memory_get_usage(TRUE) / 1048576, 2) . " Mb memory used" . PHP_EOL;
for ($n = 0; $n < 100; $n++)
{
    $documents = array();
    for ($i = 0; $i < 2000; $i++)
    {
        $placeDocument = $placeModule->createDocument(array(
            'coreItem' => array(
                'firstname' => 'Captain',
                'lastname' => 'Hindsight',
                'company' => 'Hindsight Enterprises',
                'location' => array(
                    'street' => 'Treskowstrasse',
                    'houseNumber' => '5',
                    'postalCode' => '13156',
                    'city' => 'Berlin',
                    'district' => 'Pankow',
                    'name' => 'Hindsight Headquaters'
                )
            )
        ));
        $coreItem = $placeDocument->getCoreItem();
        $coreItem->setFirstname('General');
        $coreItem->setLastname('Notfound');
        $coreItem->getLocation()->setName("Somewhere in the desert");
        $documents[] = $placeDocument;
    }
    echo "Loop number " . ($n * $i) . ", Memory used: " . 
         round(memory_get_usage(TRUE) / 1048576, 2) . " Mb " . PHP_EOL;
}
echo "And finally: " . round(memory_get_usage(TRUE) / 1048576, 2) . " Mb memory used" . PHP_EOL;
*/