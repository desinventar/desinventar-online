# Makefile (must be TAB indented)

.PHONY : all .FORCE

all : build

devel : build php js

build : composer node-build web-build portal lang

portal: .FORCE
	if [ -d portal ]; then cd portal && make; fi

composer : .FORCE
	composer install

composer-autoload : .FORCE
	composer dump-autoload --optimize

database: .FORCE
	cd files/database && make database && make update-base

lang: .FORCE
	cd files/database && make lang

php : standard-php phpmd lint-php

test : test-unit

test-unit: .FORCE
	cd tests/unit && ../../vendor/bin/phpunit --testsuite unit $(TEST)

test-api: .FORCE
	./node_modules/.bin/jest tests/api

test-e2e: .FORCE
	./node_modules/.bin/testcafe firefox:headless tests/e2e

test-portal: .FORCE
	./node_modules/.bin/testcafe firefox:headless tests/portal

lint-php : .FORCE
	find src api config web tests portal -name "*.php" -exec php -l {} > /dev/null \;

standard-php : .FORCE
	./vendor/bin/phpcs .

phpmd: .FORCE
	find api config files src tests portal/web portal/include \
		-name \*.php -exec ./vendor/bin/phpmd {} text ./files/phpmd/ruleset.xml \;

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
