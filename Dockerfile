# CentOS 6 + Updates + DesInventar Prerequisites
FROM desinventar/centos6:v20180929-01
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

WORKDIR /opt/app

RUN sed -i 's/^mirrorlist/#mirrorlist/; s|#baseurl=http://mirror.centos.org|baseurl=http://mirrors.kernel.org|' /etc/yum.repos.d/CentOS-Base.repo

ADD composer.json /tmp/composer.json
ADD composer.lock /tmp/composer.lock
RUN cd /tmp && composer install --no-scripts --no-autoloader --prefer-source --no-interaction
RUN mkdir -p /opt/app && cp -a /tmp/vendor /opt/app

ADD package.json /tmp/package.json
ADD yarn.lock /tmp/yarn.lock
RUN cd /tmp && yarn install
RUN mkdir -p /opt/app && cp -a /tmp/node_modules /opt/app

RUN yum -y install php-pecl-dbase php-pecl-zip php-redis

RUN yum -y install java
RUN mkdir -p /usr/local/liquibase && cd /usr/local/liquibase && \
    wget -q https://github.com/liquibase/liquibase/releases/download/liquibase-parent-3.5.5/liquibase-3.5.5-bin.tar.gz -O - | tar -zxf - && \
    /bin/rm -f /usr/local/bin/liquibase && ln -s /usr/local/liquibase/liquibase /usr/local/bin/liquibase
RUN cd /usr/local/liquibase && wget -q https://bitbucket.org/xerial/sqlite-jdbc/downloads/sqlite-jdbc-3.21.0.jar -O liquibase-sqlite-jdbc-3.21.0.jar
RUN cd /usr/local/liquibase && wget -q https://jdbc.postgresql.org/download/postgresql-42.2.2.jar -O liquibase-postgresql-42.2.2.jar
RUN cd /usr/local/liquibase && wget -q https://dev.mysql.com/get/Downloads/Connector-J/mysql-connector-java-5.1.46.tar.gz -O - | tar -zxf - mysql-connector-java-5.1.46/mysql-connector-java-5.1.46.jar && \
    mv mysql-connector-java-5.1.46/mysql-connector-java-5.1.46.jar ./liquibase-mysql-connector-java-5.1.46.jar

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
RUN sed -i 's#^post_max_size = 8M$#post_max_size = 100M#' /etc/php.ini
RUN sed -i 's#^upload_max_filesize = 2M$#upload_max_filesize = 100M#' /etc/php.ini

RUN sed -i 's#"files"#"redis"#' /etc/httpd/conf.d/php.conf
RUN sed -i 's#"/var/lib/php/session"#"tcp://redis:6379"#' /etc/httpd/conf.d/php.conf

COPY . /opt/app
RUN mkdir -p /opt/app && cp -a /tmp/vendor /opt/app && /bin/rm -rf /opt/app/vendor/desinventar/jpgraph && composer install
RUN mkdir -p /opt/app && cp -a /tmp/node_modules /opt/app && yarn

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
