FROM node:8-alpine
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

RUN apk add bash git curl

RUN curl https://raw.githubusercontent.com/eficode/wait-for/master/wait-for > /usr/local/bin/wait-for \
  && chmod 755 /usr/local/bin/wait-for

WORKDIR /src

# Install app dependencies using a cached layer
ADD backend/express/package.json /tmp/package.json
ADD backend/express/package-lock.json /tmp/package-lock.json
RUN cd /tmp && npm install

# Back to setting up the app itself
COPY backend/ /src/backend/
COPY config/ /src/config/
RUN cp -a /tmp/node_modules /src/backend/express/

# Expose the listening port of your app
EXPOSE 3000

CMD ["/bin/sh", "-c", "cd backend/express && npm start"]
