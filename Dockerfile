# CentOS 6 + Updates + DesInventar Prerequisites
FROM desinventar/centos6:v20170604-01
MAINTAINER Jhon H. Caicedo <jhcaiced@inticol.com>

WORKDIR /usr/share/desinventar

COPY composer.json composer.lock package.json /usr/share/desinventar/
RUN composer install --no-scripts --no-autoloader
RUN npm install

COPY files/apache/desinventar-centos-default.* /etc/httpd/conf.d/
RUN sed -i 's#logs/access_log#/dev/stderr#; s#logs/error_log#/dev/stderr#' /etc/httpd/conf/httpd.conf

COPY . /usr/share/desinventar

RUN mkdir -p /var/lib/desinventar/main/ && \
    cp files/database/{core.db,base.db,desinventar.db} /var/lib/desinventar/main/ && \
    chown -R apache:apache /var/lib/desinventar

RUN composer dump-autoload --optimize
RUN npm install
RUN make

EXPOSE 80

ENTRYPOINT [ "/usr/sbin/httpd" ]
CMD [ "-D", "FOREGROUND" ]
