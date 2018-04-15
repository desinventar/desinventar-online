FROM library/openjdk

LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

WORKDIR /opt/app

RUN mkdir -p /usr/local/liquibase && cd /usr/local/liquibase && \
    wget -q https://github.com/liquibase/liquibase/releases/download/liquibase-parent-3.5.5/liquibase-3.5.5-bin.tar.gz -O - | tar -zxf - && \
    /bin/rm -f /usr/local/bin/liquibase && ln -s /usr/local/liquibase/liquibase /usr/local/bin/liquibase

ARG EXTDIR="/usr/lib/jvm/java-8-openjdk-amd64/jre/lib/ext"
RUN cd ${EXTDIR} && wget -q https://bitbucket.org/xerial/sqlite-jdbc/downloads/sqlite-jdbc-3.21.0.jar
RUN cd ${EXTDIR} && wget -q https://jdbc.postgresql.org/download/postgresql-42.2.2.jar
RUN cd ${EXTDIR} && wget -q https://dev.mysql.com/get/Downloads/Connector-J/mysql-connector-java-5.1.46.tar.gz -O - | tar -zxf - mysql-connector-java-5.1.46/mysql-connector-java-5.1.46.jar \
  && mv mysql-connector-java-5.1.46/mysql-connector-java-5.1.46.jar .
