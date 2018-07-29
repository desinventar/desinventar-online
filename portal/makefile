# Makefile (must be TAB indented)

.PHONY : all .FORCE

all : node webpack strings

node: .FORCE
	yarn

webpack: .FORCE
	./node_modules/.bin/webpack -p

strings: files/strings/interface_strings.xml
	cd files/strings && make
#
