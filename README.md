# Dat0r [![Build Status](https://travis-ci.org/berlinonline/Dat0r.png)](https://travis-ci.org/berlinonline/Dat0r)

Dat0r is a code-generation library that was built to ease our management of domain specific data-objects in php.  
The main difference to existing php solutions, that allow generating code to handle data structures is,
that Dat0r is not an ORM and it doesn't implement any other concerns than mere data definition and containment.  
It allows to define data-structures in xml and then realizes them via code generation.  
Besides holding (complexly) structured data, we needed two more concerns to be taken care of.  
* Ensure value consistency - we don't like broken data.  
* Provide some kind of inheritance to allow reuse of structure definitions.  

Consistency is achieved by field specific validation of values.  
Values are only set if validation succeeds, so data is most surely always held as defined.  
The xml markup for defining data-structures supports nested structures and exposes a classical inheritance model,
so you can reuse and extend structures in a way that most probally is familiar.  

## Installation

This library can be used by integrating it via composer.

Add composer:

    curl -s http://getcomposer.org/installer | php

Create a 'composer.json' file with the following content:

    {
        "require": {
            "berlinonline/Dat0r": "*"
        },
        "minimum-stability": "dev"
    }

Then install via composer:

    php composer.phar install


## Usage (work in progress)

### 1. Define

After installation you are ready to write your first data-definition. 
Bellow you will find an example for the definition of a simple article object.

*article.module.xml*:

```xml
<?xml version="1.0" encoding="utf-8" ?>

<module type="Article" namespace="Example\DataObjects">
    <description>
        Articles hold news related content 
        and basically consist of a title, a paragraph and a teaser text.
    </description>
    <fields>
        <field name="title" type="text">
            <description>Holds an article's title.</description>
        </field>
        <field name="teaser" type="text">
            <description>Holds an article's teaser.</description>
        </field>
        <field name="paragraph" type="text">
            <description>Holds an article's paragraph.</description>
        </field>
        <field name="slug" type="text">
            <description>Holds an article's slug.</description>
            <!-- slugs will only be accepted if they match the following pattern -->
            <option name="pattern">/^[a-z0-9-]+$/</option>
        </field>
    </fields>
</module>

```


### 2. Generate

Before the code generation can be kicked off,
we need to create a little config file in order to control a few aspects of code generation.

*codegen.config.ini*:

```ini
; Tell Dat0r where we want generated code to be deployed.
deployDir=./data_objects

; Tell Dat0r where we want code to be generated to before deploying.
cacheDir=./codegen_cache

; Tell whether we want generated code to completely moved or just copied 
; from our cache to the deploy dir. Valid values are 'copy' or 'move'.
deployMethod=move
```

Then make sure that the directories that we configured above actually exist:

```
mkdir data_objects codegen_cache
```

To then actually generate our code we run:

```
php ./vendor/bin/gen.php -c codegen.config.ini -d article.module.xml -a gen+dep
```

This should result within an Article folder being created inside our ./data_objects directory.
The Article's file tree should look like this:

```
.
|-- ArticleDocument.php
|-- ArticleModule.php
|-- base
|   |-- BaseArticleDocument.php
|   |-- BaseArticleModule.php
```

### 3. Use

After generating the code we are now ready to make profit by using it. :)
As shown in the above file tree, two concrete classes have been generated from our definition.
The following code snippet shows an example usage of the provided API:

```php
require_once dirname(__FILE__) . '/autoload.php';

use Example\DataObjects\Article;

$module = Article\ArticleModule::getInstance();
$article = $module->createDocument(array(
    'title' => "This is an article's title.",
    'teaser' => "This is an article's teaser text.",
    'paragraph' => "This is an article's paragraph",
    'slug' => "article-example-slug"
));
$article->setTitle("This an article's changed title.");
$article->setTeaser("This an article's changed teaser text.");

var_dump($article->toArray());

foreach ($article->getChanges() as $changeEvent)
{
    var_dump($changeEvent);
}
```

An other example for integration and usage can be found at https://github.com/shrink/draftcmm

## Documentation

Dat0r is basically made up of two layers, lets call them *core-* and *domain-layer*.  
The *core-layer's* job is to actually manage data, whereas the *domain-layer's* purpose is to expose domain specific APIs.  
These APIs help us to write code for our business domains in a more clarified and less complex way,
than we could achieve when using a completely generic approach.

### Core-Layer

To manage data the *core-layer* derives meta-data from your data-structure definitions.  
This meta-data is represented by the interfaces *IModule* and *IField* and
used to create instances of your data-objects, represented by the *IDocument* interface.  
Modules hold meta-data on the *document* level and compose *fields*, that hold meta-data on the property level.  
Further more *modules* are responsable for creating *documents* based on their given meta-data.  
*Documents* are the type that actually holds the data.  
They use their *module's* *fields* to define per property behaviour such as validation or comparison
and they track state changes over time as a list of (change)events.  
In short you can say *modules* compose *fields* to realize your data-definitions and then use the latter to create *documents*.

*core-layer visualization:*

![core-layer](https://dl.dropbox.com/u/97162004/dat0r-core.png)

### Domain-Layer

Provides access to data via domain specific api that is exposed by generated data-objects.
... tbd
