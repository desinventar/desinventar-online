all : lang

lang : lang_spa lang_eng lang_por lang_fre

lang_spa : interface_strings.xml
	php ../../../scripts/xml2conf.php interface_strings.xml spa > ../../conf/spa.conf
lang_eng : interface_strings.xml
	php ../../../scripts/xml2conf.php interface_strings.xml eng > ../../conf/eng.conf
lang_por : interface_strings.xml
	php ../../../scripts/xml2conf.php interface_strings.xml por > ../../conf/por.conf
lang_fre : interface_strings.xml
	php ../../../scripts/xml2conf.php interface_strings.xml fre > ../../conf/fre.conf
