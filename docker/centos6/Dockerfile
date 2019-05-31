# CentOS 6 + Updates + DesInventar Prerequisites
# Pushed to dockerhub as desinventar/centos6:vYYYYMMDD-NN
FROM centos:centos6
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

RUN sed -i 's/^mirrorlist/#mirrorlist/; s|#baseurl=http://mirror.centos.org|baseurl=http://mirrors.kernel.org|' /etc/yum.repos.d/CentOS-Base.repo

ADD desinventar.repo /etc/yum.repos.d/desinventar.repo
RUN yum -y install epel-release
RUN yum -y update

RUN yum install -y http://opensource.wandisco.com/centos/6/git/x86_64/wandisco-git-release-6-1.noarch.rpm && \
    yum install -y git

RUN yum -y install wget unzip tar curl firefox xorg-x11-server-Xvfb

RUN yum -y install yum install httpd gd giflib sqlite
RUN yum -y install liberation-fonts-common liberation-sans-fonts
RUN yum -y install liberation-mono-fonts liberation-serif-fonts
RUN yum -y install yum-plugin-versionlock

# Update to PHP 7.2
RUN yum install -y http://rpms.remirepo.net/enterprise/remi-release-6.rpm
RUN yum -y install yum-plugin-replace yum-utils
RUN yum-config-manager --enable remi-php72
RUN yum -y install php72-php php72-php-cli php72-php-common php72-php-gd php72-php-xml php72-php-pdo php72-php-mbstring php72-php-redis php72-php-pecl-igbinary
RUN ln -s /usr/bin/php72 /usr/bin/php

RUN yum -y --enablerepo=desinventar install liberation-fonts-extras
RUN yum -y --enablerepo=epel install mapserver proj proj-epsg proj-nad
RUN cp /usr/libexec/mapserver /var/www/cgi-bin/mapserv

ADD epsg_fix.txt /tmp/epsg_fix.txt
RUN cat /tmp/epsg_fix.txt >> /usr/share/proj/epsg

RUN mkdir -p /var/www/html
RUN echo "<html><head><title>DesInventar</title></head><body><h3>DesInventar</h3></body></html>" > /var/www/html/index.html

RUN install -m 755 -o apache -g apache -d /var/local/desinventar/tmp
RUN install -m 755 -o apache -g apache -d /var/local/desinventar/db

# Install composer to handle dependencies
RUN curl --silent --show-error https://getcomposer.org/installer > /tmp/composer-setup.php && \
  php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot

# Install nvm + node + npm
ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 10.16.0
RUN curl https://raw.githubusercontent.com/creationix/nvm/v0.33.8/install.sh | bash \
    && source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

RUN npm install -g npm

EXPOSE 80

ENTRYPOINT []
CMD [ "/usr/sbin/httpd", "-D", "FOREGROUND" ]
