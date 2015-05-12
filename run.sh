#!/bin/bash

while true
do
    /nest-dashboard/app/console fetch ${NEST_USERNAME} ${NEST_PASSWORD}
    sleep 5
done
