# DesInventar RPM Spec File
# CentOS-6
# Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2016-02-22

Summary: DesInventar - Disaster Inventory System
Name: desinventar
Version: 10.01.002
Release: 1%{dist}
License: GPLv3
Group: Applications/Disaster
Source0: desinventar-online.tar.gz
Source1: http://www.cipotato.org/diva/data/misc/world_adm0.zip

Url: http://www.desinventar.org
BuildArch: noarch
BuildRoot: %{_tmppath}/%{name}-buildroot

Requires: gd giflib
Requires: sqlite >= 3.6.14
Requires: php >= 5.3
Requires: php php-common php-cli php-gd php-xml php-pdo php-mbstring
Requires: mapserver
Requires: liberation-fonts-common liberation-sans-fonts
Requires: liberation-mono-fonts liberation-serif-fonts
Requires: liberation-fonts-extras
Requires: php-dbase
Requires: php-jpgraph3
Requires: php-DrUUID

%define BASE_DIR  %{_prefix}/share/desinventar
%define WEB_DIR   %{BASE_DIR}/web
%define FILES_DIR %{BASE_DIR}/files
%define WWW_DIR   /var/www/desinventar
%define DATA_DIR  /var/lib/desinventar
%define TMP_DIR   /var/tmp/desinventar
%define CACHE_DIR /var/cache/smarty/desinventar

%description
DesInventar is a conceptual and methodological tool for the
construction of databases of loss, damage, or effects caused by emergencies
or disasters. It includes: methodology (definitions and help in the
management of data) database with flexible structure; software for input
into the database; software for consultation of data (not limited to a
predefined number of consultations) – with selection options for search
criteria.

%prep
rm -rf *
tar -zxf %{SOURCE0}

%build

%clean 
rm -rf $RPM_BUILD_ROOT

%install
# Core - Install
/bin/rm -rf $RPM_BUILD_ROOT
install -m 755 -d $RPM_BUILD_ROOT/%{BASE_DIR}

# Web Interface Install
install -m 755 -d $RPM_BUILD_ROOT/%{WEB_DIR}
cp -r * $RPM_BUILD_ROOT/%{BASE_DIR}

install -m 755 -d $RPM_BUILD_ROOT/%{DATA_DIR}

# HTTPD Conf File
#pushd .
#install -m 755 -d $RPM_BUILD_ROOT/etc/httpd/conf.d
#install -m 644 files/conf/desinventar.conf $RPM_BUILD_ROOT/etc/httpd/conf.d
#popd

# WorldMap Install
install -m 755 -d $RPM_BUILD_ROOT/%{DATA_DIR}/worldmap
cd $RPM_BUILD_ROOT/%{DATA_DIR}/worldmap
unzip %{SOURCE1}

%post
install -m 755 -o apache -g apache -d %{WWW_DIR}/graphs
install -m 755 -o apache -g apache -d %{CACHE_DIR}
install -m 755 -o apache -g apache -d %{TMP_DIR}

install -m 755 -o apache -g apache -d %{DATA_DIR}/database
install -m 755 -o apache -g apache -d %{DATA_DIR}/main

cp %{BASE_DIR}/files/database/{base.db,desinventar.db} %{DATA_DIR}/main
if [ ! -e %{DATA_DIR}/main/core.db ] ; then
    echo "Installing new core.db"
    cp %{BASE_DIR}/files/database/core.db %{DATA_DIR}/main
fi
chown -R apache:apache %{DATA_DIR}

cd %{BASE_DIR}
php -r "readfile('https://getcomposer.org/installer');" | php
./composer.phar install

%postun
cd %{BASE_DIR}
rm -rf composer.phar vendor
rm -rf %{CACHE_DIR}
rm -rf %{TMP_DIR}
rm -rf %{WWW_DIR}

%files 
%defattr(-,root,root)
#%{_prefix}/bin/*
%{_prefix}/share/desinventar/*
#%attr(-, root, root) /etc/httpd/conf.d/desinventar.conf

%changelog
* Mon Feb 22 2016 Jhon H. Caicedo <jhcaiced@desinventar.org> 10.01.002
- Updated build structure for CentOS-6
