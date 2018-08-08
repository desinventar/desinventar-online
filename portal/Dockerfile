# CentOS 6 + Updates + DesInventar Prerequisites
FROM desinventar/centos6:v20180806-01
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

RUN sed -i 's/^mirrorlist/#mirrorlist/; s|#baseurl=http://mirror.centos.org|baseurl=http://mirrors.kernel.org|' /etc/yum.repos.d/CentOS-Base.repo

WORKDIR /opt/app/portal

ADD composer.json /tmp/composer.json
ADD composer.lock /tmp/composer.lock

RUN composer self-update
RUN cd /tmp && composer install --no-scripts --no-autoloader --prefer-source --no-interaction
RUN cp -a /tmp/vendor /opt/app

ADD portal/package.json /tmp/package.json
ADD portal/yarn.lock /tmp/yarn.lock
RUN cd /tmp && yarn
RUN cp -a /tmp/node_modules /opt/app/portal

ARG DESINVENTAR_PORTAL_CACHEDIR=/var/cache/smarty/desinventar/portal
ARG DESINVENTAR_URL=https://online.desinventar.org/desinventar/
ENV DESINVENTAR_PORTAL_CACHEDIR=$DESINVENTAR_PORTAL_CACHEDIR
ENV DESINVENTAR_URL=$DESINVENTAR_URL

RUN mkdir -p /var/www/html
RUN echo "<html><head><title>DesInventar</title></head><body><h3>DesInventar</h3></body></html>" > /var/www/html/index.html
RUN install -m 755 -o apache -g apache -d $DESINVENTAR_PORTAL_CACHEDIR

RUN sed -i 's#var/www/html#/opt/app/portal/web#; s#logs/access_log#/dev/stderr#; s#logs/error_log#/dev/stderr#' /etc/httpd/conf/httpd.conf
ADD portal/files/apache/rewrite.conf /tmp/rewrite.conf
RUN cat /tmp/rewrite.conf >> /etc/httpd/conf/httpd.conf

COPY . /opt/app
RUN cp -a /tmp/vendor /opt/app
RUN cp -a /tmp/node_modules /opt/app/portal
RUN make
RUN cd /opt/app && composer install

EXPOSE 80

CMD [ "/usr/sbin/httpd", "-D", "FOREGROUND" ]
