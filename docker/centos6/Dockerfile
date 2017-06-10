# CentOS 6 + Update + DesInventar Prerequisites
FROM library/centos:centos6
MAINTAINER Jhon H. Caicedo <jhcaiced@inticol.com>

ADD desinventar.repo /etc/yum.repos.d/desinventar.repo
RUN yum -y install epel-release
RUN yum -y install git wget unzip tar

RUN yum -y install yum install httpd gd giflib sqlite
RUN yum -y install php php-common php-cli php-gd php-xml php-pdo php-mbstring
RUN yum -y install liberation-fonts-common liberation-sans-fonts
RUN yum -y install liberation-mono-fonts liberation-serif-fonts

# Update to PHP 5.6
RUN yum -y install https://mirror.webtatic.com/yum/el6/latest.rpm
RUN yum -y install yum-plugin-replace
RUN yum -y replace php-common --replace-with=php56w-common

RUN yum -y update

RUN yum -y --enablerepo=desinventar install  php56w-dbase
RUN yum -y --enablerepo=desinventar install liberation-fonts-extras php-jpgraph3 php-DrUUID
RUN yum -y --enablerepo=epel install mapserver proj proj-epsg proj-nad
RUN cp /usr/libexec/mapserver /var/www/cgi-bin/mapserv

ADD epsg_fix.txt /tmp/epsg_fix.txt
RUN cat /tmp/epsg_fix.txt >> /usr/share/proj/epsg

RUN mkdir -p /var/www/html
RUN echo "<html><head><title>DesInventar</title></head><body><h3>DesInventar</h3></body></html>" > /var/www/html/index.html

RUN install -m 755 -o apache -g apache -d /var/www/desinventar/graphs
RUN install -m 755 -o apache -g apache -d /var/lib/desinventar
RUN install -m 755 -o apache -g apache -d /var/tmp/desinventar
RUN install -m 755 -o apache -g apache -d /var/cache/smarty/desinventar
RUN install -m 755 -o apache -g apache -d /etc/desinventar

# composer
RUN curl https://getcomposer.org/composer.phar > /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 4.7.3
RUN curl https://raw.githubusercontent.com/creationix/nvm/v0.33.0/install.sh | bash \
    && source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH      $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

EXPOSE 80

ENTRYPOINT [ "/usr/sbin/httpd" ]
CMD [ "-D", "FOREGROUND" ]
