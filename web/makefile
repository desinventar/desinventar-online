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

%.js %.css:
	@
#