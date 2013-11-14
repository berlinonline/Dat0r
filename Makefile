# vim: ts=4:sw=4:noexpandtab!:

PROJECT_BASEDIR = `if [ -d ../../../vendor ]; then echo $$(cd ../../../ && pwd); else echo $$(pwd); fi`

help:
	@echo "Project base directory is: $(PROJECT_BASEDIR)"
	@echo "---------------------------------------------"
	@echo "List of available targets:"
	@echo "  code - Generate data-objects from a given module definition. Use 'make code conf={path} def={path}'"
	@echo "  install - Installs composer and all dependencies for production environments."
	@echo "  update - Updates composer and all dependencies for development environments."
	@echo "  test - Runs all test suites and publishes a code coverage report in xml and html to build/logs."
	@echo "  doc - Generates the php api documentation to the build/docs folder."
	@echo "  help - Shows this dialog."
	@exit 0

code:
	@cd $(PROJECT_BASEDIR)
	@./bin/dat0r.console generate $(conf) $(def) "gen+dep"

install: install-deps

install-deps: install-composer
	@php -d date.timezone="Europe/Berlin" ./bin/composer.phar -- install

install-composer:
	@if [ ! -d ./bin ]; then mkdir bin; fi
	@if [ ! -f ./bin/composer.phar ]; then curl -s http://getcomposer.org/installer | php -d date.timezone="Europe/Berlin" -- --install-dir=./bin/; fi

update: update-composer update-deps

update-deps: update-composer
	@php -d date.timezone="Europe/Berlin" ./bin/composer.phar -- update

update-composer: install-composer
	@php -d date.timezone="Europe/Berlin" ./bin/composer.phar -- self-update

test:
	@$(PROJECT_BASEDIR)/vendor/bin/phpunit

doc:
	@if [ -d ./build/docs ]; then rm -rf ./build/docs; fi
	@php $(PROJECT_BASEDIR)/vendor/bin/sami.php update ./config/sami.php

code-sniffer:

	@mkdir -p ./build/logs
	-@./vendor/bin/phpcs --extensions=php --report=checkstyle --report-file=./build/logs/checkstyle.xml --standard=psr2 ./src ./tests/src

code-sniffer-cli:

	-@./vendor/bin/phpcs --extensions=php --standard=psr2 ./src

.PHONY: test help code doc install update
