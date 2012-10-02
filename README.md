# CMF

[![Build Status](https://travis-ci.org/shrink/CMF.png)](https://travis-ci.org/shrink/CMF)

This library is a prototype for yet another data mapper based on code generation.
The idea is to provide a lightweight (php)solution for defining and managing data structures in form of data objects.
More description and docs are up the pipeline.

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
