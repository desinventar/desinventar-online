# DesInventar RPM Spec File
# CentOS-6
# Jhon H. Caicedo <jhcaiced@desinventar.org>
# 2016-06-21

Summary: DesInventar - Disaster Inventory System
Name: desinventar
Version: 10.01.005
Release: 1.el6
License: GPLv3
Group: Applications/Disaster
Source0: desinventar-online.tar.gz
Source1: http://www.cipotato.org/diva/data/misc/world_adm0.zip

Url: http://www.desinventar.org
BuildArch: noarch
BuildRoot: %{_tmppath}/%{name}-buildroot

Requires: httpd gd giflib
Requires: sqlite >= 3.6.14
Requires: php >= 5.3
Requires: php php-common php-cli php-gd php-xml php-pdo php-mbstring
Requires: liberation-fonts-common liberation-sans-fonts
Requires: liberation-mono-fonts liberation-serif-fonts
Requires: liberation-fonts-extras
Requires: php-dbase php-jpgraph3
Requires: mapserver proj proj-epsg proj-nad

%define BASE_DIR  %{_prefix}/share/desinventar
%define WEB_DIR   %{BASE_DIR}/web
%define FILES_DIR %{BASE_DIR}/files
%define WWW_DIR   /var/www/desinventar
%define DATA_DIR  /var/lib/desinventar
%define TMP_DIR   /var/tmp/desinventar
%define CACHE_DIR /var/cache/smarty/desinventar
%define CONFIG_DIR /etc/desinventar

%description
DesInventar is a conceptual and methodological tool for the
construction of databases of loss, damage, or effects caused by emergencies
or disasters. It includes: methodology (definitions and help in the
management of data) database with flexible structure; software for input
data into the database; software for querying the database in a flexible manner
with multiple selection options and search criteria.

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

# WorldMap Install
install -m 755 -d $RPM_BUILD_ROOT/%{DATA_DIR}/worldmap
cd $RPM_BUILD_ROOT/%{DATA_DIR}/worldmap
unzip %{SOURCE1}

install -m 755 -d $RPM_BUILD_ROOT/%{CONFIG_DIR}
cp $RPM_BUILD_ROOT/%{BASE_DIR}/config/config.php $RPM_BUILD_ROOT/%{CONFIG_DIR}/config.php

%posttrans
install -m 755 -o apache -g apache -d %{WWW_DIR}/graphs
rm -rf %{CACHE_DIR}/*
install -m 755 -o apache -g apache -d %{CACHE_DIR}
rm -rf %{TMP_DIR}/*
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
rm -rf composer.phar

%files
%defattr(-,root,root)
%{_prefix}/share/desinventar/*
/var/lib/desinventar/worldmap/*
%config %{CONFIG_DIR}/config.php

%changelog
* Thu Jul 14 2016 Jhon H. Caicedo <jhcaiced@inticol.com> 10.01.005
- Updated for new release

* Sun Jun 19 2016 Jhon H. Caicedo <jhcaiced@desinventar.org> 10.01.004
- Updated for new release

* Sun Jun 12 2016 Jhon H. Caicedo <jhcaiced@desinventar.org> 10.01.003
- Updated for new release

* Mon Feb 22 2016 Jhon H. Caicedo <jhcaiced@desinventar.org> 10.01.002
- Updated build structure for CentOS-6
