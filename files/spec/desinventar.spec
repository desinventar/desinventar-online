# Try to detect the distribution for some specific build options
%define mydist %(cat ~/.rpmmacros  | grep 'dist ' | sed -e 's/ //g' | cut -d'.' -f2)
%define isCentOS4 %(if [ "%{mydist}" == "centos4" ]; then echo "1"; else echo "0"; fi;)

Summary: DesInventar - Disaster Inventory System
Name: desinventar
Version: 8.1.9
Release: 3%{dist}
License: Propietary
Group: Applications/Disaster
Source0: %{name}-%{version}.tar.gz
Source1: http://www.cipotato.org/diva/data/misc/world_adm0.zip
Source2: world_adm0.map

Url: http://www.desinventar.org
BuildArch: noarch
BuildRoot: %{_tmppath}/%{name}-buildroot

# In CentOS-4 use the Sun JRE/JDK packages for Java
%if %{isCentOS4}
BuildRequires: jdk
Requires: jre
%else
BuildRequires: java-1.4.2-gcj-compat-devel
Requires: java-1.4.2-gcj-compat
%endif

BuildRequires: mysql-server
BuildRequires: mysql-connector-java-bin
Requires: mysql-connector-java-bin ostermillerutils-bin
Requires: xmlrpc-bin backport-util-concurrent-bin
Requires: perl perl-Frontier-RPC
Requires: php >= 5.1.0
Requires: php-gd php-mysql php-Smarty phpxmlrpc
Requires: extJS jpgraph
Requires: mysql-server httpd
Requires: openlayers mapserver
Requires: liberation-fonts-extras

%define DI_DIR    %{_prefix}/share/desinventar
%define WEB_DIR   %{DI_DIR}/web
%define FILES_DIR %{DI_DIR}/files
%define WWW_DIR   /var/www/desinventar
%define DATA_DIR  /var/lib/desinventar
%define CACHE_DIR /var/cache/Smarty/di8

%description
DesInventar is a conceptual and methodological tool for the
construction of databases of loss, damage, or effects caused by emergencies
or disasters. It includes: methodology (definitions and help in the
management of data) database with flexible structure; software for input
into the database; software for consultation of data (not limited to a
predefined number of consultations) – with selection options for search
criteria.

%prep
%setup -q
mkdir worldmap
cd worldmap
unzip %{SOURCE1}

%build
# Core - Compile
cd core
if [ -d classes ]; then
	rmdir classes
fi
mkdir classes
make jar

%clean 
rm -rf $RPM_BUILD_ROOT

%install
# Core - Install
pushd .
cd core
make install DESTDIR=$RPM_BUILD_ROOT/%{_prefix}
cd $RPM_BUILD_ROOT/%{DI_DIR}
mv dicore.jar dicore-%{version}.jar
ln -s dicore-%{version}.jar dicore.jar
install -m 755 -d $RPM_BUILD_ROOT/%{_prefix}/bin
cd $RPM_BUILD_ROOT/%{_prefix}/bin
rm -f dicore-control
ln -s ../share/desinventar/dicore-control.pl dicore-control
popd

# Web Interface Install
pushd .
install -m 755 -d $RPM_BUILD_ROOT/%{WEB_DIR}
cd web
cp -r * $RPM_BUILD_ROOT/%{WEB_DIR}
cd ..
install -m 755 -d $RPM_BUILD_ROOT/%{FILES_DIR}
install -m 644 files/dictionary/di8doc.sq3 $RPM_BUILD_ROOT/%{FILES_DIR}
install -m 755 -d $RPM_BUILD_ROOT/%{WWW_DIR}/graphs
install -m 755 -d $RPM_BUILD_ROOT/%{WWW_DIR}/logo
install -m 755 -d $RPM_BUILD_ROOT/%{DATA_DIR}
install -m 755 -d $RPM_BUILD_ROOT/%{DATA_DIR}/carto
install -m 755 -d $RPM_BUILD_ROOT/%{DATA_DIR}/logo
install -m 755 -d $RPM_BUILD_ROOT/%{DATA_DIR}/maps
install -m 755 -d $RPM_BUILD_ROOT/%{CACHE_DIR}/templates_c
popd

# HTTPD Conf File
pushd .
install -m 755 -d $RPM_BUILD_ROOT/etc/httpd/conf.d
install -m 644 conf/httpd/desinventar.conf $RPM_BUILD_ROOT/etc/httpd/conf.d
popd

# WorldMap Install
pushd .
cd worldmap
install -m 755 -d $RPM_BUILD_ROOT/%{_datadir}/desinventar/worldmap/
install -m 644 * $RPM_BUILD_ROOT/%{_datadir}/desinventar/worldmap/
install -m 644 %{SOURCE2} $RPM_BUILD_ROOT/%{_datadir}/desinventar/worldmap/worldmap.map

popd

%files 
%defattr(-,root,root)
%{_prefix}/bin/*
%{_prefix}/share/desinventar/*
%attr(-, root, root) /etc/httpd/conf.d/desinventar.conf
%attr(-, apache, root) %{WWW_DIR}
%attr(-, apache, root) %{DATA_DIR}
%attr(-, apache, root) %{CACHE_DIR}/*

%changelog
* Tue Dec 16 2008 Jhon H. Caicedo <jhcaiced@desinventar.org> 8.1.9.centos5
- rpm build for CentOS-5
- first attempt to build an rpm
