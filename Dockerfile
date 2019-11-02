# CentOS 6 + Updates + DesInventar Prerequisites
FROM desinventar/centos6:v20190531-01
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

WORKDIR /opt/app

RUN sed -i 's/^mirrorlist/#mirrorlist/; s|#baseurl=http://mirror.centos.org|baseurl=http://mirrors.kernel.org|' /etc/yum.repos.d/CentOS-Base.repo

RUN yum -y install php72-php-pecl-dbase php72-php-pecl-zip php72-php-pecl-redis
RUN yum -y install yum-plugin-versionlock && yum versionlock php72-php-pecl-dbase-7.0.0

RUN composer config --global repo.packagist composer https://packagist.org
RUN composer global require hirak/prestissimo

ADD composer.json /tmp/composer.json
ADD composer.lock /tmp/composer.lock
RUN cd /tmp && composer install --no-scripts --no-autoloader --no-interaction

ADD package.json /tmp/package.json
ADD package-lock.json /tmp/package-lock.json
RUN cd /tmp && npm install

RUN useradd -g 33 -u 33 www-data
# Apache Configuration
RUN sed -i 's#logs/access_log#/dev/stderr#; s#logs/error_log#/dev/stderr#' /etc/httpd/conf/httpd.conf
RUN sed -i 's#User apache#User www-data#' /etc/httpd/conf/httpd.conf

COPY files/apache/desinventar-centos-default.* /etc/httpd/conf.d/
RUN sed -i 's#/var/www/html#/opt/app#' /etc/httpd/conf.d/desinventar-centos-default.conf
RUN sed -i 's#/var/www/html#/opt/app#' /etc/httpd/conf.d/desinventar-centos-default.inc
COPY files/apache/desinventar-centos-default.conf /etc/httpd/conf.d/web.conf
RUN sed -i 's#/var/www/html#/opt/app#' /etc/httpd/conf.d/web.conf
RUN sed -i 's#localhost#desinventaronline_web_1#' /etc/httpd/conf.d/web.conf
RUN sed -i 's#^post_max_size = 8M$#post_max_size = 100M#' /etc/opt/remi/php72/php.ini
RUN sed -i 's#^upload_max_filesize = 2M$#upload_max_filesize = 100M#' /etc/opt/remi/php72/php.ini

RUN sed -i 's#"files"#"redis"#' /etc/httpd/conf.d/php72-php.conf
RUN sed -i 's#"/var/opt/remi/php72/lib/php/session"#"tcp://redis:6379"#' /etc/httpd/conf.d/php72-php.conf

COPY . /opt/app
RUN cp -a /tmp/vendor /opt/app && /bin/rm -rf /opt/app/vendor/desinventar/jpgraph && composer install
RUN cp -a /tmp/node_modules /opt/app && npm install

RUN make devel-app
RUN make database

RUN mkdir -p /var/local/desinventar/db/main/ && \
    cp files/database/{core.db,base.db,desinventar.db} /var/local/desinventar/db/main/ && \
    chown -R www-data:apache /var/local/desinventar/db
RUN mkdir -p /var/local/desinventar/worldmap && \
    unzip -o files/worldmap/world_adm0.zip -d /var/local/desinventar/worldmap && \
    cp files/worldmap/world_adm0.map /var/local/desinventar/worldmap/ && \
    chown -R www-data:apache /var/local/desinventar/worldmap
RUN mkdir -p /var/local/desinventar/db/database && \
    chown -R www-data:apache /var/local/desinventar/db/database
RUN mkdir -p /var/tmp/desinventar && chown -R www-data:apache /var/tmp/desinventar

COPY files/seed/seed.tar.gz /tmp
RUN cd /var/local/desinventar/db && \
    tar -zxf /tmp/seed.tar.gz && \
    chown -R www-data:apache /var/local/desinventar/db

RUN chown -R www-data:apache /var/local/desinventar

ENV PATH="~/.composer/vendor/bin:./vendor/bin:/opt/app/vendor/bin:${PATH}"

EXPOSE 80

ENTRYPOINT []
CMD [ "/usr/sbin/httpd", "-D", "FOREGROUND" ]
