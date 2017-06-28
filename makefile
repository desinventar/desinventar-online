# Makefile (must be TAB indented)

.PHONY : all .FORCE

all : build devel

devel : php js

build : npm-build composer lang

composer : .FORCE
	composer install

composer-autoload : .FORCE
	composer dump-autoload --optimize

lang : .FORCE
	cd files/database && make lang

php : standard-php phpmd lint-php

test : test-unit

test-unit: .FORCE
	cd tests && ../vendor/bin/phpunit --testsuite unit

test-web: .FORCE
	cd tests && ../vendor/bin/phpunit --testsuite web

lint-php : .FORCE
	find src api config web tests -name "*.php" -exec php -l {} > /dev/null \;

standard-php : .FORCE
	./vendor/bin/phpcs --standard=PSR2 src/* api/app/* api/web/* \
	tests/bootstrap.php tests/Unit/* tests/WebTest/* \
	config/config.php config/version.php

phpmd: .FORCE
	find api config files portal src tests -name \*.php -exec ./vendor/bin/phpmd {} text ./files/phpmd/ruleset.xml \;

phpmd-old: .FORCE
	find web -name \*.php -exec ./vendor/bin/phpmd {} text ./files/phpmd/ruleset.xml \;

js : standard-js

standard-js : .FORCE
	./node_modules/.bin/eslint web/js2/*

npm-build : .FORCE
	npm install
