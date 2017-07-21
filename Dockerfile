# CentOS 6 + Updates + DesInventar Prerequisites
FROM desinventar/centos6:v20170610-01
MAINTAINER Jhon H. Caicedo <jhcaiced@inticol.com>

WORKDIR /usr/share/desinventar

COPY composer.json composer.lock package.json /usr/share/desinventar/
RUN composer install --no-scripts --no-autoloader --prefer-source --no-interaction
RUN npm install

COPY files/apache/desinventar-centos-default.* /etc/httpd/conf.d/
RUN sed -i 's#logs/access_log#/dev/stderr#; s#logs/error_log#/dev/stderr#' /etc/httpd/conf/httpd.conf
COPY files/apache/desinventar-centos-default.conf /etc/httpd/conf.d/web.conf
RUN sed -i 's#localhost#desinventaronline_web_1#' /etc/httpd/conf.d/web.conf

COPY . /usr/share/desinventar

RUN mkdir -p /var/lib/desinventar/main/ && \
    cp files/database/{core.db,base.db,desinventar.db} /var/lib/desinventar/main/ && \
    chown -R apache:apache /var/lib/desinventar
RUN mkdir -p /var/lib/desinventar/worldmap && \
    unzip files/worldmap/world_adm0.zip -d /var/lib/desinventar/worldmap && \
    cp files/worldmap/world_adm0.map /var/lib/desinventar/worldmap/ && \
    chown -R apache:apache /var/lib/desinventar/worldmap
    
RUN composer dump-autoload --optimize
RUN npm install
RUN make

ENV PATH="~/.composer/vendor/bin:./vendor/bin:/usr/share/desinventar/vendor/bin:${PATH}"

EXPOSE 80

ENTRYPOINT [ "/usr/sbin/httpd" ]
CMD [ "-D", "FOREGROUND" ]