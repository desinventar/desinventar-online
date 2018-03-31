# Makefile (must be TAB indented)

.PHONY : all .FORCE

all : build

build : webpack strings

webpack: .FORCE
	./node_modules/.bin/webpack -p

strings: files/strings/interface_strings.xml
	cd files/strings && make
#
