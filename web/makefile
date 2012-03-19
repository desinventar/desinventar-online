JQUERY_SCRIPTS=\
	/usr/share/jquery/jquery.min.js \
	/usr/share/jquery/ui/jquery-ui.min.js \
	/usr/share/jquery/tooltip/jquery.tooltip.min.js \
	/usr/share/jquery/dimensions/jquery.dimensions.js \
	/usr/share/jquery/jquery.stringQuery.js \
	/usr/share/jquery/jquery.dataSelector.js \
	/usr/share/jquery/jquery.ba-bbq.min.js

JS_SCRIPTS=\
	json2.js \
	form2js.js \
	js2form.js \
	jquery.toObject.js \
	jquery.jec-1.3.3.js \
	listMan.js \
	md5.js \
	wd.js \
	accordion.js \
	palette.js \
	checktree/checktree.js \
	Math.uuid.js

all : desinventar.js jquery.desinventar.js other.js

desinventar.js : js/*.js
	cd js && (cat *.js > ../scripts/desinventar.js)

other.js:
	cd external && (cat $(JS_SCRIPTS) > ../scripts/other.js)

jquery.desinventar.js : 
	cd js && (cat $(JQUERY_SCRIPTS) > ../scripts/jquery.desinventar.js)
	
%.js %.css:
	@
#