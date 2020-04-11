# Ubuntu + Update + Testing Tools
# Pushed to dockerhub as desinventar/test:vYYYYMMDD-NN
FROM ubuntu:bionic
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update
RUN apt-get -y install git wget tar curl firefox php-cli

# Install composer to handle dependencies
# Use latest 1.x version (2.x version doesn't work because of some of the required modules)
RUN curl --silent --show-error https://getcomposer.org/download/1.10.1/composer.phar > /usr/local/bin/composer && chmod 755 /usr/local/bin/composer

RUN apt-get -q -y install git curl lsb-release software-properties-common apt-transport-https make

# Install Docker
RUN curl -fsSL https://download.docker.com/linux/ubuntu/gpg | apt-key add - \
  && add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" \
  && apt-get -q update \
  && apt-get install -q -y docker-ce

# Docker Compose
RUN curl -s -L https://github.com/docker/compose/releases/download/1.25.4/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose \
  && chmod +x /usr/local/bin/docker-compose

# Install nvm + node + npm
ENV NVM_DIR /usr/bin
ENV NODE_VERSION 10.20.0
RUN curl https://raw.githubusercontent.com/creationix/nvm/v0.35.3/install.sh | bash \
  && . $NVM_DIR/nvm.sh \
  && nvm install $NODE_VERSION \
  && nvm alias default $NODE_VERSION \
  && nvm use default
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH
