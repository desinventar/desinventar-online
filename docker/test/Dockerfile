# Ubuntu + Update + Testing Tools
# Pushed to dockerhub as desinventar/test:vYYYYMMDD-NN
FROM ubuntu:xenial
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

RUN apt-get -y update
RUN apt-get -y install git wget tar curl firefox php-cli

# Install composer to handle dependencies
RUN curl --silent --show-error https://getcomposer.org/installer > /tmp/composer-setup.php && \
  php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot

RUN apt-get -q -y install git curl lsb-release software-properties-common apt-transport-https make

# Install Docker
RUN curl -fsSL https://download.docker.com/linux/ubuntu/gpg | apt-key add - \
  && add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" \
  && apt-get -q update \
  && apt-get install -q -y docker-ce

# Docker Compose
RUN curl -s -L https://github.com/docker/compose/releases/download/1.19.0/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose \
  && chmod +x /usr/local/bin/docker-compose

# Install nvm + node + npm
ENV NVM_DIR /usr/bin
ENV NODE_VERSION 8.9.4
RUN curl https://raw.githubusercontent.com/creationix/nvm/v0.33.8/install.sh | bash \
  && . $NVM_DIR/nvm.sh \
  && nvm install $NODE_VERSION \
  && nvm alias default $NODE_VERSION \
  && nvm use default
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

# Install yarn
RUN mkdir -p /opt/yarn \
  && cd /opt/yarn \
  && wget https://github.com/yarnpkg/yarn/releases/download/v1.3.2/yarn-v1.3.2.tar.gz \
  && tar zxf yarn-v1.3.2.tar.gz \
  && mv yarn-v1.3.2 dist
ENV PATH="/opt/yarn/dist/bin:${PATH}"
