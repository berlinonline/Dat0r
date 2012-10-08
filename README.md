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

```sh
curl -s http://getcomposer.org/installer | php
```

Create a 'composer.json' file with the following content:

```json
{
    "require": {
        "berlinonline/Dat0r": "*"
    },
    "minimum-stability": "dev"
}
```

Then install via composer:

```sh
php composer.phar install
```

## Usage

### 1. Define

After installation you are ready to write your first data-definition.  
Below you will find an example for the definition of a simple article object.

*article.module.xml*:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<!--
Holds the definition of a data structure that makes up an example module named Article.
-->
<module name="Article" namespace="Example\DataObject">
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

The next step after defining our desired data structure is to generate the corresponding code.  
Before the code generation can be kicked off,
we need to create a little config file in order to control a few aspects of code generation.  
The contents of the config file we are using for this example is listed below.

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

Then make sure, that the directories which we configured above actually exist:

```sh
mkdir data_objects codegen_cache
```

To then actually generate the code we run:

```sh
php ./vendor/berlinonline/dat0r/bin/generate_code.php -c codegen.config.ini -d article.module.xml -a gen+dep
```

This should result within an Article folder being created inside our ./data_objects directory.  
The Article directory's file tree should look like this:

```
`data_objects/
`-- Article
    |-- ArticleDocument.php
    |-- ArticleModule.php
    `-- Base
        |-- ArticleDocument.php
        `-- ArticleModule.php
```

### 3. Use

After generating the code we are now ready to make profit by using it. :)  
As shown in the above file tree, two concrete and two abstract classes have been generated from our definition.
In order to get those classes autoloaded we will need to create a small autoload file for our example.  

*autoload.php:*

```php
<?php
// require the vendor/composer autoload.
require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// then register our generated package to Dat0r.
Dat0r\Autoloader::register(array(
    'Example\DataObject' => __DIR__ . DIRECTORY_SEPARATOR . 'data_objects'
));
```

We now have autoload support for the Example\DataObject namespace.  
The following code snippet shows an example usage of the API provided by the concrete implementations.  

*dat0r_example.php:*

```php
<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

use Example\DataObject\Article;

$module = Article\ArticleModule::getInstance();
$article = $module->createDocument(array(
    'title' => "This is an article's title.",
    'teaser' => "This is an article's teaser text.",
    'paragraph' => "This is an article's paragraph",
    'slug' => "article-example-slug"
));
$article->setTitle("This an article's changed title.");
$article->setTeaser("This an article's changed teaser text.");

foreach ($article->getChanges() as $idx => $changeEvent)
{
    printf("- event number %d:\n%s\n", $idx + 1, $changeEvent);
}

printf("- current data:\n%s", print_r($article->toArray(), TRUE));
```

When running the example ...

```sh
php dat0r_example.php
```

... we should receive the following output:

```
- event number 1:
The `title` field's value changed from 'This is an article's title.' to 'This an article's changed title.'
- event number 2:
The `teaser` field's value changed from 'This is an article's teaser text.' to 'This an article's changed teaser text.'
- current data:
Array
(
    [title] => This an article's changed title.
    [teaser] => This an article's changed teaser text.
    [paragraph] => This is an article's paragraph
    [slug] => article-example-slug
)
```

For further details on the available core level API
consult the <a href="https://github.com/berlinonline/Dat0r#api-doc">API Doc</a> section.  
An other example for integration and usage can be found at https://github.com/shrink/draftcmm

## Documentation

### Architecture

Dat0r is basically made up of two layers, lets call them *core-* and *domain-layer*.  
The *core-layer's* job is to actually manage data, whereas the *domain-layer's* purpose is to expose domain specific APIs.  
These APIs help us to write code for our business domains in a more clarified and less complex way,
than we could achieve when using a completely generic approach.  

*layers visualization:*

![dat0r-layers](https://dl.dropbox.com/u/97162004/dat0r-layers.png)

#### Core-Layer

To manage data the *core-layer* derives meta-data from your data-structure definitions.  
This meta-data is represented by the interfaces *IModule* and *IField* and
used to create instances of your data-objects, represented by the *IDocument* interface.  
Modules hold meta-data on the *document* level and compose *fields*, that hold meta-data on the property level.  
Further more *modules* are responsable for creating *documents* based on their given meta-data.  
*Documents* are the type that actually holds the data.  
They use their *module's* *fields* to define per property behaviour such as validation or comparison
and they track state changes over time as a list of (change)events.  
In short you can say *modules* compose *fields* to realize your data-definitions
and then use the latter to create *documents* that hold the data.  
Below you will find diagram that shows the how the core-layer's components and how they play together.  

*core-layer visualization:*

![core-layer](https://dl.dropbox.com/u/97162004/dat0r-core.png)

#### Domain-Layer

Sitting on top of the generic core-layer, the *domain-layer* uses generated classes to provide an interface,
that is dedicated to the domains described within our data definitions.  
The *domain-layer* acts upon two levels of abstraction that we'll call *base-* and *custom-level*.  

##### Base-Level

The *base-level* code connects our generated domain specific *modules* and *documents* with the *core-layer*.   
As the *core-layer* provides us with generic default implementations for a given structure definition,
it is the *base-level's* job to define and pass these concrete definitions to the *core-layer*.  
Usually the only places you'll find *base-level* code are the auto-generated Base* classes.  
Listed below is an example showing how structure information is propagated from the *domain-layer*
to the underlying *core-layer* by using inheritance to provide the specific definitions to the core.  
The code is an excerpt from the results of the above <a href="#2-generate">code generation example</a>.  

```php
<?php

namespace Example\DataObject\Article;

abstract class BaseArticleModule extends \Dat0r\Core\Runtime\Module\RootModule
{
    protected function __construct()
    {
        return parent::__construct('Article', array( 
            \Dat0r\Core\Runtime\Field\TextField::create('title'), 
            \Dat0r\Core\Runtime\Field\TextField::create('teaser'), 
            \Dat0r\Core\Runtime\Field\TextField::create('paragraph'), 
            \Dat0r\Core\Runtime\Field\TextField::create('slug', array(                                 
                'pattern' => '/^[a-z0-9-]+$/',  
            )),         
        ));
    }

    /**
     * Returns the IDocument implementor to use when creating new documents.
     *
     * @return string Fully qualified name of an IDocument implementation.
     */
    protected function getDocumentImplementor()
    {
        return 'Example\DataObject\Article\ArticleDocument';
    }
}
```

##### Custom-Level

The *custom-level's* purpose lies in providing a place for us to easily customize behaviour.  
Whenever a *core-layer* implementation doesn't fit our needs, the *custom-layer* is the place to put hands on.  
Referring to the file tree <a href="#2-generate">in the usage example section</a>,
the ArticleModule and ArticleDocument classes would represent the *custom-level* implementations of the Article definition.  
By default these are empty skeletons,
that are ready to override or extend any default behaviour for *modules* and *documents*.  

### API Doc

http://berlinonline.github.com/Dat0r/
