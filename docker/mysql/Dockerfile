FROM mysql:5.7
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

COPY docker/mysql/server.cnf /etc/mysql/mysql.conf.d/server.cnf

RUN echo "\
  CREATE USER desinventar@'%'; \
  CREATE DATABASE IF NOT EXISTS desinventar_core; \
  GRANT ALL ON desinventar_core.* TO desinventar@'%'; \
  FLUSH PRIVILEGES; \
  " \
>> /docker-entrypoint-initdb.d/init.sql
