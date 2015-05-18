#!/bin/bash

while true
do
    echo -e "nest:\n  username: $NEST_USERNAME\n  password: $NEST_PASSWORD\nopenweather:\n  city_id: $OPENWEATHERMAP_CITYID" > /nest-dashboard/app/config/config.yml
    /nest-dashboard/app/console fetch
    sleep 30
done
