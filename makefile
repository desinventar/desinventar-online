# Makefile (must be TAB indented)

.PHONY: all .FORCE

all: build

devel-app: build-app php-basic js

devel: build php js

build-app: composer node-build web-build lang

build: build-app portal

portal: .FORCE
	if [ -d portal ]; then cd portal && make; fi

composer: .FORCE
	composer install

composer-autoload: .FORCE
	composer dump-autoload --optimize

database: .FORCE
	npm run db
	cd files/database && make database

lang: .FORCE
	cd files/database && make lang

php-basic: php-lint phpcs phpmd

php: php-basic phpstan

test: test-unit test-api

test-unit: .FORCE
	./vendor/bin/phpunit

test-api: test-api-app test-api-portal

test-api-app: .FORCE
	TEST_API_URL=http://localhost:8080 ./node_modules/.bin/jest tests/api-app

test-api-portal: .FORCE
	TEST_PORTAL_URL=http://localhost:8090 ./node_modules/.bin/jest tests/api-portal

test-e2e: .FORCE
	TEST_WEB_URL=http://localhost:8080 ./node_modules/.bin/testcafe firefox tests/e2e

test-portal: .FORCE
	TEST_PORTAL_URL=http://localhost:8090 TEST_PORTAL_USERNAME=root TEST_PORTAL_PASSWD=desinventar ./node_modules/.bin/testcafe firefox tests/portal

php-lint: .FORCE
	find src config web tests portal -name "*.php" -exec php -l {} > /dev/null \;

phpcs: .FORCE
	./vendor/bin/phpcs .

phpcs-security: .FORCE
	sh vendor/pheromone/phpcs-security-audit/symlink.sh && \
	./vendor/bin/phpcs -np --standard=./vendor/pheromone/phpcs-security-audit/example_base_ruleset.xml portal scripts src tests web

phpmd: .FORCE
	find config files src/Api src/DesInventar/Common src/DesInventar/Models src/DesInventar/Services src/DesInventar/Actions tests portal \
		-name \*.php -exec ./vendor/bin/phpmd {} text phpmd.xml \;

phpstan: .FORCE
	./vendor/bin/phpstan analyse --level 7 src tests

js: standard-js

standard-js: .FORCE
	./node_modules/.bin/eslint .

node-build: .FORCE
	npm install

web-build: .FORCE
	./node_modules/.bin/webpack -p

clean: .FORCE
	cd files/database && make clean
