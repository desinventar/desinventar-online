#!/bin/bash
#
docker run \
    --env TEST_PORTAL_URL=http://portal:80 \
    --env TEST_PORTAL_USERNAME=root \
    --env TEST_PORTAL_PASSWD=desinventar \
    -v `pwd`:/opt/app \
    --network desinventar_default \
    desinventar/e2e-test:latest sh -c 'make test-portal'
