# vim: ts=4:sw=4:noexpandtab!:

PROJECT_BASEDIR = `if [ -d ../../../vendor ]; then echo $$(cd ../../../ && pwd); else echo $$(pwd); fi`

metrics:
	@if [ -d ./build/codebrowser ]; then rm -rf ./build/codebrowser; fi
	@mkdir -p ./build/codebrowser
	@mkdir -p ./build/logs

	@$(PROJECT_BASEDIR)/vendor/bin/phpunit
	@$(PROJECT_BASEDIR)/vendor/bin/phpcs --extensions=php --report=checkstyle --report-file=./build/logs/checkstyle.xml --standard=psr2 ./src ./tests
	-@$(PROJECT_BASEDIR)/vendor/bin/phpcpd --log-pmd ./build/logs/pmd-cpd.xml src/
	-@$(PROJECT_BASEDIR)/vendor/bin/phpmd src/ xml codesize,design,naming,unusedcode --reportfile ./build/logs/pmd.xml
	-@$(PROJECT_BASEDIR)/vendor/bin/phpcb --log ./build/logs/ --source ./src --output ./build/codebrowser/

install:
	@if [ ! -d ./bin ]; then mkdir bin; fi
	@if [ ! -f ./bin/composer.phar ]; then curl -s http://getcomposer.org/installer | php -d date.timezone="Europe/Berlin" -- --install-dir=./bin/; fi
	@php -d date.timezone="Europe/Berlin" ./bin/composer.phar -- install

update:
	@php -d date.timezone="Europe/Berlin" ./bin/composer.phar -- update
	@php -d date.timezone="Europe/Berlin" ./bin/composer.phar -- self-update

api-doc:
	@if [ -d ./build/docs ]; then rm -rf ./build/docs; fi
	@php $(PROJECT_BASEDIR)/vendor/bin/sami.php update ./config/sami.php

code-sniffer:
	-@$(PROJECT_BASEDIR)/vendor/bin/phpcs --extensions=php --standard=psr2 ./src/ ./tests

test:
	@$(PROJECT_BASEDIR)/vendor/bin/phpunit --bootstrap ./tests/bootstrap.php --no-configuration tests/Dat0r

.PHONY: test api-doc install update metrics code-sniffer
