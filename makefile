# Makefile (must be TAB indented)

.PHONY : all .FORCE

all : standard-php standard-js

standard-php : .FORCE
	./vendor/bin/phpcs --standard=PSR2 src/* api/* config/config.php config/version.php

standard-js : .FORCE
	./node_modules/.bin/eslint web/js2/*

#