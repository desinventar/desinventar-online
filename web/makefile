JQUERY_SCRIPTS=\
	/usr/share/jquery/jquery.min.js \
	/usr/share/jquery/ui/jquery-ui.min.js \
	/usr/share/jquery/tooltip/jquery.tooltip.min.js \
	/usr/share/jquery/dimensions/jquery.dimensions.js \
	/usr/share/jquery/jquery.stringQuery.js \
	/usr/share/jquery/jquery.dataSelector.js

all : desinventar.js jquery.desinventar.js

desinventar.js : js/*.js
	cd js && (cat *.js > ../scripts/desinventar.js)

jquery.desinventar.js : 
	cd js && (cat $(JQUERY_SCRIPTS) > ../scripts/jquery.desinventar.js)
	
%.js %.css:
	@
#