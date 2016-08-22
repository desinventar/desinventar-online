# Makefile (must be TAB indented)

.PHONY : all .FORCE

all : lint-php standard-php standard-js

lint-php : .FORCE
	bash ./scripts/lint.sh

standard-php : .FORCE
	./vendor/bin/phpcs --standard=PSR2 src/* api/* config/config.php config/version.php

standard-js : .FORCE
	./node_modules/.bin/eslint web/js2/*

#