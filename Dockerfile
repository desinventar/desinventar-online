# CentOS 6 + Updates + DesInventar Prerequisites
FROM desinventar/centos6:v20180401-01
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

WORKDIR /opt/app

ADD composer.json /tmp/composer.json
ADD composer.lock /tmp/composer.lock
RUN cd /tmp && composer install --no-scripts --no-autoloader --prefer-source --no-interaction
RUN mkdir -p /opt/app && cp -a /tmp/vendor /opt/app

ADD package.json /tmp/package.json
ADD yarn.lock /tmp/yarn.lock
RUN cd /tmp && yarn install
RUN mkdir -p /opt/app && cp -a /tmp/node_modules /opt/app

RUN composer self-update

RUN yum -y install java
RUN mkdir -p /usr/local/liquibase && cd /usr/local/liquibase && \
    wget -q https://github.com/liquibase/liquibase/releases/download/liquibase-parent-3.5.5/liquibase-3.5.5-bin.tar.gz -O - | tar -zxf - && \
    /bin/rm /usr/local/bin/liquibase && ln -s /usr/local/liquibase/liquibase /usr/local/bin/liquibase
RUN cd /usr/local/liquibase && wget -q https://bitbucket.org/xerial/sqlite-jdbc/downloads/sqlite-jdbc-3.21.0.jar
RUN cd /usr/local/liquibase && wget -q https://jdbc.postgresql.org/download/postgresql-42.2.2.jar
RUN cd /usr/local/liquibase && wget -q https://dev.mysql.com/get/Downloads/Connector-J/mysql-connector-java-5.1.46.tar.gz -O - | tar -zxf - mysql-connector-java-5.1.46/mysql-connector-java-5.1.46.jar && \
    mv mysql-connector-java-5.1.46/mysql-connector-java-5.1.46.jar .

# Apache Configuration
RUN sed -i 's#logs/access_log#/dev/stderr#; s#logs/error_log#/dev/stderr#' /etc/httpd/conf/httpd.conf
COPY files/apache/desinventar-centos-default.* /etc/httpd/conf.d/
RUN sed -i 's#/var/www/html#/opt/app#' /etc/httpd/conf.d/desinventar-centos-default.conf
RUN sed -i 's#/var/www/html#/opt/app#' /etc/httpd/conf.d/desinventar-centos-default.inc
COPY files/apache/desinventar-centos-default.conf /etc/httpd/conf.d/web.conf
RUN sed -i 's#/var/www/html#/opt/app#' /etc/httpd/conf.d/web.conf
RUN sed -i 's#localhost#desinventaronline_web_1#' /etc/httpd/conf.d/web.conf
RUN sed -i 's#^post_max_size = 8M$#post_max_size = 100M#' /etc/php.ini
RUN sed -i 's#^upload_max_filesize = 2M$#upload_max_filesize = 100M#' /etc/php.ini

COPY . /opt/app
RUN mkdir -p /opt/app && cp -a /tmp/vendor /opt/app
RUN mkdir -p /opt/app && cp -a /tmp/node_modules /opt/app

RUN make

RUN mkdir -p /var/local/desinventar/db/main/ && \
    cp files/database/{core.db,base.db,desinventar.db} /var/local/desinventar/db/main/ && \
    chown -R apache:apache /var/local/desinventar/db
RUN mkdir -p /var/local/desinventar/worldmap && \
    unzip -o files/worldmap/world_adm0.zip -d /var/local/desinventar/worldmap && \
    cp files/worldmap/world_adm0.map /var/local/desinventar/worldmap/ && \
    chown -R apache:apache /var/local/desinventar/worldmap
RUN mkdir -p /var/local/desinventar/db/database && \
    chown -R apache:apache /var/local/desinventar/db/database
RUN mkdir -p /var/local/desinventar/tmp/{data,graphs,maps} && \
    chown -R apache:apache /var/local/desinventar/tmp

COPY files/seed/seed.tar.gz /tmp
RUN cd /var/local/desinventar/db && \
    tar -zxf /tmp/seed.tar.gz && \
    chown -R apache:apache /var/local/desinventar/db

ENV PATH="~/.composer/vendor/bin:./vendor/bin:/opt/app/vendor/bin:${PATH}"

EXPOSE 80

ENTRYPOINT []
CMD [ "/usr/sbin/httpd", "-D", "FOREGROUND" ]
