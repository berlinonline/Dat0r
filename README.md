# Dat0r 

[![Build Status](https://travis-ci.org/berlinonline/Dat0r.png)](https://travis-ci.org/berlinonline/Dat0r)

## Abstract

When crafting code we think that it is essential to correctly distinguish and separate concerns.
We believe that decoupled components are less painfull to maintain and easier to reuse.

Dat0r is a code-generation library that was built to ease our management of domain specific data-objects in php.
The main difference to existing php solutions, that allow generating code to handle data structures is,
that Dat0r is not an ORM and it doesn't implement any other concerns than defining and holding data.
It allows to define data-structures in xml and then realizes them via code generation.
Besides holding (complexly) structured data, we needed two more concerns to be taken care of.
* Ensure value consistency - we don't like broken data.
* Provide some kind of inheritance to allow reuse of structure definitions.

Consistency is achieved by field specific validation of values.
Values are only set if validation succeeds, so data is most surely always held as defined.
The xml markup used to define data-structures allows to nest structure definitions
and supports data-object inheritance so you can reuse and extend common structure definitions.

## Installation

This library can be used by integrating it via composer.

Add composer: 

    curl -s http://getcomposer.org/installer | php

Create a 'composer.json' file with the following content:

    {
        "require": {
            "shrink/cmf": "*"
        },
        "minimum-stability": "dev"
    }

Then install via composer:

    php composer.phar install


## Usage

An example for integration and usage can be found at https://github.com/shrink/draftcmm

## Documentation

Dat0r is basically made up of two layers, lets call them "core"- and "domain"-layer.
The core-layer's job is to actually manage data, whereas the domain-layer's purpose is to expose domain specific APIs.

### Core Layer

To manage data the core-layer derives meta-data from your data-structure definitions.
This meta-data is represented by the interfaces "IModule" and "IField" and
used to create instances of your data-objects, represented by the "IDocument" interface.
Modules hold meta-data on the document level and compose Fields, that hold meta-data on the property level.
Further more modules are responsable for creating documents based on their given meta-data.
Documents are the type that actually holds the data.
They use their module's fields to define per property behaviour such as validation or comparison
and they track state changes over time as a list of (change)events.
In short you can say "Module"s compose "Field"s to realize your data-definitions and then use  the latter to create "Document"s.

*Core layer visualization:*

![core-layer](https://dl.dropbox.com/u/97162004/dat0r-core.png)

### Domain Layer

Provides access to data via domain specific api that is exposed by generated data-objects.
... tbd