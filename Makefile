# vim: ts=4:sw=4:noexpandtab!:

help:
	@echo "List of available targets:"
	@echo "  install - Installs composer and all dependencies."
	@echo "  update - Updates composer and all dependencies."
	@echo "  test - Runs all test suites and publishes an code coverage report in xml and html."
	@echo "  docs - Generates the php api doc."
	@echo "  help - Shows this dialog."
	@exit 0

test:
	@if [ ! -d ./test/reports ]; then mkdir ./test/reports; fi
	@./vendor/bin/phpunit -c ./test/phpunit.xml.dist

docs:
	@php ./vendor/bin/phpdoc.php --config ./docs/phpdoc.xml

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

.PHONY: test help code docs install update
