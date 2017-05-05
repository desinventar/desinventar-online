# Makefile (must be TAB indented)

.PHONY : all .FORCE

all : build devel

devel : php js

build : npm-build composer lang

lang : .FORCE
	cd files/database && make lang

php : standard-php lint-php

composer : .FORCE
	composer install

lint-php : .FORCE
	bash ./scripts/lint.sh

standard-php : .FORCE
	./vendor/bin/phpcs --standard=PSR2 src/* api/app/* api/web/* api/src/* \
	config/config.php config/version.php

js : standard-js

standard-js : .FORCE
	./node_modules/.bin/eslint web/js2/*

npm-build : .FORCE
	npm prune & npm install
