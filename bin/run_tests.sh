#/usr/env bash

BIN_DIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
TEST_DIR="$( cd $BIN_DIR/../test && pwd )"

$BIN_DIR/../vendor/bin/phpunit -c $TEST_DIR/phpunit.xml.dist
