JQUERY_SCRIPTS=\
	/usr/share/jquery/jquery.min.js \
	/usr/share/jquery/ui/jquery-ui.min.js \
	/usr/share/jquery/tooltip/jquery.tooltip.min.js \
	/usr/share/jquery/dimensions/jquery.dimensions.js \
	/usr/share/jquery/jquery.stringQuery.js \
	/usr/share/jquery/jquery.dataSelector.js

all : desinventar.js jquery.desinventar.js

desinventar.js : js/*.js
	cd js && rm -f desinventar.js && (cat *.js > desinventar.js)

jquery.desinventar.js : 
	cd js && rm -f jquery.desinventar.js && (cat $(JQUERY_SCRIPTS) > jquery.desinventar.js)
	
%.js %.css:
	@
#