#!/bin/bash

docker run -d -p 8080:80 --name desinventar desinventar/centos6

# Debug in interactive mode
#docker run -it --rm -p 8080:80 desinventar/centos6


