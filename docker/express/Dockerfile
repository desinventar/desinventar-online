FROM node:8-alpine
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

RUN apk add bash git curl
RUN npm install -g yarn

RUN curl https://raw.githubusercontent.com/eficode/wait-for/master/wait-for > /usr/local/bin/wait-for \
  && chmod 755 /usr/local/bin/wait-for

WORKDIR /src
#RUN mkdir -p /src

# Install app dependencies using a cached layer
ADD package.json /tmp/package.json
ADD yarn.lock /tmp/yarn.lock
RUN cd /tmp && yarn install

# Back to setting up the app itself
COPY backend/ /src/backend/
COPY config/ /src/config/
COPY package.json /src/
RUN ls /src
RUN cp -a /tmp/node_modules /src/backend/express/

# Expose the listening port of your app
EXPOSE 3000

CMD ["/bin/sh", "-c", "cd backend/express && npm start"]
