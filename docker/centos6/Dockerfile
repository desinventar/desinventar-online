# CentOS 6 + Updates + DesInventar Prerequisites
# Pushed to dockerhub as desinventar/centos6:vYYYYMMDD-NN
FROM library/centos:centos6
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

ADD desinventar.repo /etc/yum.repos.d/desinventar.repo
RUN yum -y install epel-release
RUN yum -y update

RUN yum install -y http://opensource.wandisco.com/centos/6/git/x86_64/wandisco-git-release-6-1.noarch.rpm && \
    yum install -y git

RUN yum -y install wget unzip tar

RUN yum -y install yum install httpd gd giflib sqlite
RUN yum -y install php php-common php-cli php-gd php-xml php-pdo php-mbstring
RUN yum -y install liberation-fonts-common liberation-sans-fonts
RUN yum -y install liberation-mono-fonts liberation-serif-fonts

# Update to PHP 5.6
RUN yum -y install https://mirror.webtatic.com/yum/el6/latest.rpm
RUN yum -y install yum-plugin-replace
RUN yum -y replace php-common --replace-with=php56w-common

RUN yum -y --enablerepo=desinventar install  php56w-dbase
RUN yum -y --enablerepo=desinventar install liberation-fonts-extras
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

# Install composer to handle dependencies
RUN curl --silent --show-error https://getcomposer.org/installer > /tmp/composer-setup.php && \
  php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot

ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 8.6.0
RUN curl https://raw.githubusercontent.com/creationix/nvm/v0.33.4/install.sh | bash \
    && source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

EXPOSE 80

ENTRYPOINT [ "/usr/sbin/httpd" ]
CMD [ "-D", "FOREGROUND" ]
