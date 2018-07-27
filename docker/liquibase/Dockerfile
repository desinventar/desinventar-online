FROM library/openjdk
LABEL maintainer="Jhon H. Caicedo"
LABEL e-mail="jhcaiced@inticol.com"

RUN apt-get update
RUN apt-get install -y nginx sqlite3 make php-cli

RUN mkdir -p /usr/local/liquibase && cd /usr/local/liquibase && \
    wget -q https://github.com/liquibase/liquibase/releases/download/liquibase-parent-3.5.5/liquibase-3.5.5-bin.tar.gz -O - | tar -zxf - && \
    /bin/rm -f /usr/local/bin/liquibase && ln -s /usr/local/liquibase/liquibase /usr/local/bin/liquibase
RUN cd /usr/local/liquibase && wget -q https://bitbucket.org/xerial/sqlite-jdbc/downloads/sqlite-jdbc-3.21.0.jar -O liquibase-sqlite-jdbc-3.21.0.jar
RUN cd /usr/local/liquibase && wget -q https://jdbc.postgresql.org/download/postgresql-42.2.2.jar -O liquibase-postgresql-42.2.2.jar
RUN cd /usr/local/liquibase && wget -q https://dev.mysql.com/get/Downloads/Connector-J/mysql-connector-java-5.1.46.tar.gz -O - | tar -zxf - mysql-connector-java-5.1.46/mysql-connector-java-5.1.46.jar && \
    mv mysql-connector-java-5.1.46/mysql-connector-java-5.1.46.jar ./liquibase-mysql-connector-java-5.1.46.jar

WORKDIR /opt/app

CMD [ "/usr/sbin/nginx", "-g", "daemon off;" ]
