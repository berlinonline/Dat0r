# Dat0r 

[![Build Status](https://travis-ci.org/berlinonline/Dat0r.png)](https://travis-ci.org/berlinonline/Dat0r)

## Abstract

When crafting code we think that it is essential to correctly distinguish and separate concerns.
We believe that decoupled components are less painfull to maintain and easier to reuse.

Dat0r is a code-generation library that was built to ease our management of domain specific data-objects in php.
The main difference to existing php solutions, that allow generating code to handle data structures is,
that Dat0r is not an ORM and it doesn't implement any other persistence concerns.
It just takes care of defining data-structures and realizing them via code generation.
Besides plain containers for our data, we needed two more concerns to be respected by the lib.
* Ensure value consistency - no one wants ambigious data to be let in.
* Support complex structure definitions such as aggregated/nested objects or inheriting from other data-objects.

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

### Architecture

Dat0r is basically made up of two layers, lets call them "core"- and "domain"-layer.
The core-layer's job is to actually manage data, whereas the domain-layer's purpose is to expose domain specific APIs.

#### Core Layer

The core-layer provides access to the meta-data that is derived from your data-object definitions.
It mainly consists of three interfaces named "IModule", "IField" and "IDocument".
... tbd

#### Domain Layer

Provides access to data via domain specific api that is exposed by generated data-objects.
... tbd
