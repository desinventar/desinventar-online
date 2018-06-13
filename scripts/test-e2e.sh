#!/bin/bash
#
docker run \
    --env TEST_WEB_URL=http://web:80 \
    -v `pwd`:/opt/app \
    --network desinventar_default \
    desinventar/e2e-test:latest sh -c 'make test-e2e'
