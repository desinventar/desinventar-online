FROM thingswise/mapserver

RUN apt-get update
RUN apt-get install -y fonts-liberation
RUN cd /usr/share/fonts && ln -s truetype/liberation
COPY files/fonts/fonts.txt /usr/share/fonts/truetype/liberation/
