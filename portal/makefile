# Makefile (must be TAB indented)

.PHONY : all .FORCE

all : composer node webpack strings

composer: .FORCE
	composer install

node: .FORCE
	yarn

webpack: .FORCE
	./node_modules/.bin/webpack -p

strings: files/strings/interface_strings.xml
	cd files/strings && make
#
