# CentOS 6 + Updates + DesInventar Prerequisites
FROM library/centos:centos6
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

RUN sed -i 's/^mirrorlist/#mirrorlist/; s|#baseurl=http://mirror.centos.org|baseurl=http://mirrors.kernel.org|' /etc/yum.repos.d/CentOS-Base.repo

RUN yum -y install epel-release
RUN yum -y install wget tar which

RUN yum -y install yum install httpd
RUN yum -y install php php-common php-cli php-gd php-xml php-pdo php-mbstring

# Update to PHP 5.6
RUN yum -y install https://mirror.webtatic.com/yum/el6/latest.rpm \
    && yum -y install yum-plugin-replace \
    && yum -y replace php-common --replace-with=php56w-common

RUN yum install -y http://opensource.wandisco.com/centos/6/git/x86_64/wandisco-git-release-6-1.noarch.rpm && \
    yum install -y git

# composer
RUN curl https://getcomposer.org/composer.phar > /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

# Install nvm + node + npm + yarn
ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 8.10.0
RUN curl https://raw.githubusercontent.com/creationix/nvm/v0.33.8/install.sh | bash \
    && source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

RUN npm install -g yarn

WORKDIR /opt/app/portal

ADD composer.json /tmp/composer.json
ADD composer.lock /tmp/composer.lock

RUN composer self-update
RUN cd /tmp && composer install --no-scripts --no-autoloader --prefer-source --no-interaction
RUN cp -a /tmp/vendor /opt/app/portal

ADD package.json /tmp/package.json
ADD yarn.lock /tmp/yarn.lock
RUN cd /tmp && yarn
RUN cp -a /tmp/node_modules /opt/app/portal

ARG DESINVENTAR_PORTAL_CACHEDIR=/var/cache/smarty/desinventar/portal
ARG DESINVENTAR_URL=https://online.desinventar.org/desinventar/
ENV DESINVENTAR_CACHEDIR=$DESINVENTAR_PORTAL_CACHEDIR
ENV DESINVENTAR_URL=$DESINVENTAR_URL

RUN mkdir -p /var/www/html
RUN echo "<html><head><title>DesInventar</title></head><body><h3>DesInventar</h3></body></html>" > /var/www/html/index.html
RUN install -m 755 -o apache -g apache -d $DESINVENTAR_CACHEDIR

RUN sed -i 's#var/www/html#/opt/app/portal/web#; s#logs/access_log#/dev/stderr#; s#logs/error_log#/dev/stderr#' /etc/httpd/conf/httpd.conf
ADD files/apache/rewrite.conf /tmp/rewrite.conf
RUN cat /tmp/rewrite.conf >> /etc/httpd/conf/httpd.conf

COPY . /opt/app/portal

RUN make composer
RUN make node

EXPOSE 80

CMD [ "/usr/sbin/httpd", "-D", "FOREGROUND" ]
