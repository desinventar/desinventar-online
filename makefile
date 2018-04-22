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

test : test-unit

test-unit: .FORCE
	cd tests/unit && ../../vendor/bin/phpunit --testsuite unit $(TEST)

test-api: .FORCE
	./node_modules/.bin/jest tests/api

test-e2e: .FORCE
	./node_modules/.bin/testcafe firefox:headless tests/e2e

lint-php : .FORCE
	find src api config portal web tests -name "*.php" -exec php -l {} > /dev/null \;

standard-php : .FORCE
	./vendor/bin/phpcs .

phpmd: .FORCE
	find api config files portal/web portal/include src tests -name \*.php -exec ./vendor/bin/phpmd {} text ./files/phpmd/ruleset.xml \;

phpstan: .FORCE
	docker run --rm -v $(PWD):/app phpstan/phpstan analyse --level 7 /app/src /app/tests

js : standard-js

standard-js : .FORCE
	./node_modules/.bin/eslint web/js2/**/*.js tests/e2e/**/*.js

node-build : .FORCE
	yarn install

web-build: .FORCE
	./node_modules/.bin/webpack -p

clean: .FORCE
	cd files/database && make clean
