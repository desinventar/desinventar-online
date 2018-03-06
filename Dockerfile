# CentOS 6 + Updates + DesInventar Prerequisites
FROM desinventar/centos6:v20180224-01
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

WORKDIR /usr/share/desinventar

ADD composer.json /tmp/composer.json
ADD composer.lock /tmp/composer.lock
RUN cd /tmp && composer install --no-scripts --no-autoloader --prefer-source --no-interaction
RUN mkdir -p /usr/share/desinventar && cp -a /tmp/vendor /usr/share/desinventar

ADD package.json /tmp/package.json
ADD yarn.lock /tmp/yarn.lock
RUN cd /tmp && yarn install
RUN mkdir -p /usr/share/desinventar && cp -a /tmp/node_modules /usr/share/desinventar

RUN composer self-update

COPY files/apache/desinventar-centos-default.* /etc/httpd/conf.d/
RUN sed -i 's#logs/access_log#/dev/stderr#; s#logs/error_log#/dev/stderr#' /etc/httpd/conf/httpd.conf
COPY files/apache/desinventar-centos-default.conf /etc/httpd/conf.d/web.conf
RUN sed -i 's#localhost#desinventaronline_web_1#' /etc/httpd/conf.d/web.conf
RUN sed -i 's#^post_max_size = 8M$#post_max_size = 100M#' /etc/php.ini
RUN sed -i 's#^upload_max_filesize = 2M$#upload_max_filesize = 100M#' /etc/php.ini

COPY . /usr/share/desinventar
RUN mkdir -p /usr/share/desinventar && cp -a /tmp/vendor /usr/share/desinventar
RUN mkdir -p /usr/share/desinventar && cp -a /tmp/node_modules /usr/share/desinventar

RUN make

RUN mkdir -p /var/local/desinventar/db/main/ && \
    cp files/database/{core.db,base.db,desinventar.db} /var/local/desinventar/db/main/ && \
    chown -R apache:apache /var/local/desinventar/db
RUN mkdir -p /var/local/desinventar/worldmap && \
    unzip files/worldmap/world_adm0.zip -d /var/local/desinventar/worldmap && \
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

ENV PATH="~/.composer/vendor/bin:./vendor/bin:/usr/share/desinventar/vendor/bin:${PATH}"

EXPOSE 80

ENTRYPOINT [ "/usr/sbin/httpd" ]
CMD [ "-D", "FOREGROUND" ]
