#!/bin/bash

#docker ps -a | grep 'weeks ago' | awk '{print $1}' | xargs --no-run-if-empty docker rm

docker stop desinventar
docker rm desinventar
