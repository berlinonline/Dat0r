# vim: ts=4:sw=4:noexpandtab!:

help:

	@echo "Possible targets:"
	@echo "  test - build all test suites"
	@echo "  install-dependencies - install composer if necessary and install or update all vendor libraries"
	@exit 0

test:

	@./bin/run_tests.sh

docs:

	@./bin/generate_docs.sh

code:

	@php ./bin/generate_code.php

install: install-dependencies

install-dependencies: install-composer

	@make install-composer
	@php -d date.timezone="Europe/Berlin" ./bin/composer.phar -- update

install-composer:

	@if [ ! -d ./bin ]; then mkdir bin; fi
	@if [ ! -f ./bin/composer.phar ]; then curl -s http://getcomposer.org/installer | php -d date.timezone="Europe/Berlin" -- --install-dir=./bin/; fi
	
.PHONY: test help code docs
