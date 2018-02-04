# Makefile (must be TAB indented)

.PHONY : all .FORCE

all : build devel

devel : php js

build : node-build web-build composer lang database

composer : .FORCE
	composer install

composer-autoload : .FORCE
	composer dump-autoload --optimize

database: .FORCE
	cd files/database && make all

lang : .FORCE
	cd files/database && make lang

php : standard-php phpmd lint-php

test : test-unit test-api

test-unit: .FORCE
	cd tests/unit && ../../vendor/bin/phpunit --testsuite unit $(TEST)

test-api: .FORCE
	cd tests/unit && ../../vendor/bin/phpunit --testsuite api $(TEST)

test-web: .FORCE
	xvfb-run '--server-args=-screen 0 1024x768x24' node_modules/.bin/testcafe firefox tests/e2e

lint-php : .FORCE
	find src api config web tests -name "*.php" -exec php -l {} > /dev/null \;

standard-php : .FORCE
	./vendor/bin/phpcs .

phpmd: .FORCE
	find api config files src tests -name \*.php -exec ./vendor/bin/phpmd {} text ./files/phpmd/ruleset.xml \;

phpmd-old: .FORCE
	find web -name \*.php -exec ./vendor/bin/phpmd {} text ./files/phpmd/ruleset.xml \;

js : standard-js

standard-js : .FORCE
	./node_modules/.bin/eslint web/js2/*

node-build : .FORCE
	yarn

web-build: .FORCE
	./node_modules/.bin/webpack -p

clean: .FORCE
	cd files/database && make clean
