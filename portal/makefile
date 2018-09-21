# Makefile (must be TAB indented)

.PHONY : all .FORCE

all : node webpack lang

node: .FORCE
	yarn

webpack: .FORCE
	./node_modules/.bin/webpack -p

lang: lang_spa lang_eng lang_por lang_fre

lang_spa: files/strings/interface_strings.xml
	php ../scripts/xml2conf.php --file=files/strings/interface_strings.xml --language=spa > conf/spa.conf
lang_eng: files/strings/interface_strings.xml
	php ../scripts/xml2conf.php --file=files/strings/interface_strings.xml --language=eng > conf/eng.conf
lang_por: files/strings/interface_strings.xml
	php ../scripts/xml2conf.php --file=files/strings/interface_strings.xml --language=por > conf/por.conf
lang_fre: files/strings/interface_strings.xml
	php ../scripts/xml2conf.php --file=files/strings/interface_strings.xml --language=fre > conf/fre.conf

#
