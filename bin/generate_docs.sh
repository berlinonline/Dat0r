#/usr/env bash

BIN_DIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
DOCS_DIR="$( cd $BIN_DIR/../docs && pwd )"
LIB_DIR="$( cd $BIN_DIR/../lib && pwd )"

$BIN_DIR/../vendor/bin/phpdoc.php --config $DOCS_DIR/phpdoc.xml
