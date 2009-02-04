# ***********************************************
# DesInventar
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
# ***********************************************
JAVA=java
JAVAC=javac
JAVAC_FLAGS=-encoding UTF-8 -source 1.4 -g -Xlint -extdirs /usr/share/java-ext

JAR=jar
PROGRAM=dicore
MAINCLASS=org.desinventar.core.DIServer

SOURCES= \
org/desinventar/core/Auth.java \
org/desinventar/core/Constants.java \
org/desinventar/core/DatacardLockList.java \
org/desinventar/core/DICause.java \
org/desinventar/core/DIDatabaseInfo.java \
org/desinventar/core/DIDatabaseLog.java \
org/desinventar/core/DIDisaster.java \
org/desinventar/core/DIEvent.java \
org/desinventar/core/DIEEField.java \
org/desinventar/core/DIEEData.java \
org/desinventar/core/DIFormatter.java \
org/desinventar/core/DIGeography.java \
org/desinventar/core/DIGeoLevel.java \
org/desinventar/core/DIImport.java \
org/desinventar/core/DIObject.java \
org/desinventar/core/DIRegionInfo.java \
org/desinventar/core/DIServer.java \
org/desinventar/core/DIUser.java \
org/desinventar/core/DIUtil.java \
org/desinventar/core/Role.java \
org/desinventar/core/RpcDIServer.java \
org/desinventar/core/RpcQueryOperations.java \
org/desinventar/core/RpcSample.java \
org/desinventar/core/RpcSessionOperations.java \
org/desinventar/core/RpcRegionOperations.java \
org/desinventar/core/UserSession.java \
org/desinventar/core/UserSessionList.java \
org/desinventar/core/Util.java
#org/desinventar/core/test/DesInventarTest.java

$(PROGRAM): $(SOURCES)
	$(JAVAC) $(JAVAC_FLAGS) -d classes $(SOURCES)
exe: $(SOURCES)
	gcj $(JAVAC_FLAGS) --main=$(MAINCLASS) -o $(PROGRAM) $(SOURCES)

$(PROGRAM).jar: $(PROGRAM)-classes Manifest
	$(JAR) cvfm $(PROGRAM).jar Manifest -C classes .
#	$(JAR) cvfm $(PROGRAM).jar Manifest org/desinventar/core/*.class
#	cd ..
Manifest:
	echo "Main-Class: $(MAINCLASS)" > Manifest

classes:
	mkdir classes

$(PROGRAM)-classes: classes $(SOURCES)
	$(JAVAC) $(JAVAC_FLAGS) -d classes $(SOURCES)

all: $(PROGRAM) $(PROGRAM.jar) # $(PROGRAM)-static

jar: $(PROGRAM).jar

clean:
	rm -rf classes/* Manifest $(PROGRAM) $(PROGRAM)-static $(PROGRAM).jar

.PHONY : all clean $(PROGRAM)-classes

test: all
	cd classes ; java -Dfile.encoding=utf-8 -Djava.ext.dirs=/usr/share/java-ext org/desinventar/core/DIServer test; cd ..
#	cd classes ; java -Xmx256M -Djava.ext.dirs=/usr/share/java-ext org/desinventar/core/DIServer test; cd ..
server:
	cd classes ; java -Dfile.encoding=utf-8 -Xmx256M -Djava.ext.dirs=/usr/share/java-ext:/usr/share/java org/desinventar/core/DIServer; cd ..

jarserver: jar
	cd classes ; $(JAVA) -Dfile.encoding=utf-8 -Djava.ext.dirs=/usr/share/java-ext -jar $(PROGRAM).jar; cd ..

install: jar
	install -m 755 -d $(DESTDIR)/share/desinventar
	install -m 644 dicore.jar $(DESTDIR)/share/desinventar
	install -m 755 scripts/dicore-control.pl $(DESTDIR)/share/desinventar
#