#/usr/env bash

BIN_DIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

$BIN_DIR/../vendor/bin/phpunit -c $BIN_DIR/../test/phpunit.xml.dist
