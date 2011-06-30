# Try to detect the distribution for some specific build options
%define mydist %(cat ~/.rpmmacros  | grep 'dist ' | sed -e 's/ //g' | cut -d'.' -f2)
%define isCentOS4 %(if [ "%{mydist}" == "centos4" ]; then echo "1"; else echo "0"; fi;)

Summary: DesInventar - Disaster Inventory System
%define name1 desinventar
%define major 82
Name: %{name1}%{major}
Version: 2011.056
Release: 1%{dist}
License: Propietary
Group: Applications/Disaster
Source0: %{name1}-%{version}.tar.gz
Source1: http://www.cipotato.org/diva/data/misc/world_adm0.zip
Source2: world_adm0.map

Url: http://www.desinventar.org
BuildArch: noarch
BuildRoot: %{_tmppath}/%{name}-buildroot

Requires: php >= 5.1.0
Requires: php-gd php-Smarty
Requires: extJS jpgraph
Requires: httpd
Requires: openlayers mapserver
Requires: liberation-fonts-extras
Requires: sqlite >= 3.6.14
Requires: jquery jquery-ui jquery-uploadify jquery-colorpicker

%define DI_DIR    %{_prefix}/share/desinventar-8.2
%define WEB_DIR   %{DI_DIR}/web
%define FILES_DIR %{DI_DIR}/files
%define WWW_DIR   /var/www/desinventar-8.2
%define DATA_DIR  /var/lib/desinventar-8.2
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
%setup -q -n %{name1}-%{version}
mkdir worldmap
cd worldmap
unzip %{SOURCE1}

%build

%clean 
rm -rf $RPM_BUILD_ROOT

%install
# Core - Install
pushd .
/bin/rm -rf $RPM_BUILD_ROOT
install -m 755 -d $RPM_BUILD_ROOT/%{DI_DIR}
popd

# Web Interface Install
pushd .
install -m 755 -d $RPM_BUILD_ROOT/%{WEB_DIR}
cd web
cp -r * $RPM_BUILD_ROOT/%{WEB_DIR}
cd ..
install -m 755 -d $RPM_BUILD_ROOT/%{FILES_DIR}
install -m 755 -d $RPM_BUILD_ROOT/%{WWW_DIR}/graphs
install -m 755 -d $RPM_BUILD_ROOT/%{WWW_DIR}/logo
install -m 755 -d $RPM_BUILD_ROOT/%{DATA_DIR}
install -m 755 -d $RPM_BUILD_ROOT/%{DATA_DIR}/main
cd files/database
install -m 644 *.db $RPM_BUILD_ROOT/%{DATA_DIR}/main
cd ../..
install -m 755 -d $RPM_BUILD_ROOT/%{DATA_DIR}/database
install -m 755 -d $RPM_BUILD_ROOT/%{CACHE_DIR}
install -m 755 -d $RPM_BUILD_ROOT/%{CACHE_DIR}/templates_c
popd

# HTTPD Conf File
pushd .
install -m 755 -d $RPM_BUILD_ROOT/etc/httpd/conf.d
install -m 644 files/conf/desinventar-8.2.conf $RPM_BUILD_ROOT/etc/httpd/conf.d
popd

# WorldMap Install
pushd .
cd worldmap
install -m 755 -d $RPM_BUILD_ROOT/%{_datadir}/desinventar-8.2/worldmap/
install -m 644 * $RPM_BUILD_ROOT/%{_datadir}/desinventar-8.2/worldmap/
install -m 644 %{SOURCE2} $RPM_BUILD_ROOT/%{_datadir}/desinventar-8.2/worldmap
install -m 644 %{SOURCE2} $RPM_BUILD_ROOT/%{_datadir}/desinventar-8.2/worldmap/worldmap.map

popd

%files 
%defattr(-,root,root)
#%{_prefix}/bin/*
%{_prefix}/share/desinventar-8.2/*
%attr(-, root, root) /etc/httpd/conf.d/desinventar-8.2.conf
%attr(-, apache, root) %{WWW_DIR}
%attr(-, apache, root) %{DATA_DIR}
%attr(-, apache, root) %{CACHE_DIR}
%attr(-, apache, root) %{CACHE_DIR}/*
%config %{DATA_DIR}/main/core.db

%changelog
* Tue Sep  1 2009 Jhon H. Caicedo <jhcaiced@desinventar.org> 8.2.0.44
- updated version and create separate directories for each version
  of application
