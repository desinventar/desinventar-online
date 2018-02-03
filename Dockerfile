# CentOS 6 + Updates + DesInventar Prerequisites
FROM desinventar/centos6:v20171022-01
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

WORKDIR /usr/share/desinventar

RUN composer self-update
RUN npm install -g yarn

ADD composer.json /tmp/composer.json
ADD composer.lock /tmp/composer.lock
RUN cd /tmp && composer install --no-scripts --no-autoloader --prefer-source --no-interaction
RUN mkdir -p /usr/share/desinventar && cp -a /tmp/vendor /usr/share/desinventar

ADD package.json /tmp/package.json
ADD yarn.lock /tmp/yarn.lock
RUN cd /tmp && yarn install
RUN mkdir -p /usr/share/desinventar && cp -a /tmp/node_modules /usr/share/desinventar

COPY files/apache/desinventar-centos-default.* /etc/httpd/conf.d/
RUN sed -i 's#logs/access_log#/dev/stderr#; s#logs/error_log#/dev/stderr#' /etc/httpd/conf/httpd.conf
COPY files/apache/desinventar-centos-default.conf /etc/httpd/conf.d/web.conf
RUN sed -i 's#localhost#desinventaronline_web_1#' /etc/httpd/conf.d/web.conf
RUN sed -i 's#^post_max_size = 8M$#post_max_size = 100M#' /etc/php.ini
RUN sed -i 's#^upload_max_filesize = 2M$#upload_max_filesize = 100M#' /etc/php.ini

COPY . /usr/share/desinventar

RUN make database
RUN mkdir -p /var/lib/desinventar/main/ && \
    cp files/database/{core.db,base.db,desinventar.db} /var/lib/desinventar/main/ && \
    chown -R apache:apache /var/lib/desinventar
RUN mkdir -p /var/lib/desinventar/worldmap && \
    unzip files/worldmap/world_adm0.zip -d /var/lib/desinventar/worldmap && \
    cp files/worldmap/world_adm0.map /var/lib/desinventar/worldmap/ && \
    chown -R apache:apache /var/lib/desinventar/worldmap
RUN mkdir -p /var/lib/desinventar/database && \
    chown -R apache:apache /var/lib/desinventar/database
RUN mkdir -p /var/www/desinventar/data && \
    chown -R apache:apache /var/www/desinventar/data

COPY files/seed/seed.tar.gz /tmp
RUN cd /var/lib/desinventar && \
    tar -zxf /tmp/seed.tar.gz && \
    chown -R apache:apache /var/lib/desinventar

RUN composer dump-autoload --optimize
RUN yarn install
RUN make
RUN ./node_modules/.bin/webpack -p

ENV PATH="~/.composer/vendor/bin:./vendor/bin:/usr/share/desinventar/vendor/bin:${PATH}"

EXPOSE 80

ENTRYPOINT [ "/usr/sbin/httpd" ]
CMD [ "-D", "FOREGROUND" ]
