FROM desinventar/test:v20190531-01
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

WORKDIR /opt/app
RUN apt-get update && apt-get install -y php

ADD package.json /tmp/package.json
ADD package-lock.json /tmp/package-lock.json
RUN cd /tmp && npm install

COPY . /opt/app
RUN cp -a /tmp/node_modules /opt/app
ENV PATH="~/.composer/vendor/bin:./vendor/bin:/opt/app/vendor/bin:${PATH}"

EXPOSE 80
CMD [ "/usr/bin/php", "-S", "localhost:80" ]
