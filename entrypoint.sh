#!/bin/sh

mkdir -p /nest-dashboard/app/config
echo date.timezone = \"$DEFAULT_TIMEZONE\">/usr/local/etc/php/conf.d/timezone.ini

while true
do
    echo -e "nest:\n  username: $NEST_USERNAME\n  password: $NEST_PASSWORD\nopenweather:\n  city_id: $OPENWEATHERMAP_CITYID\n  app_id: $OPENWEATHERMAP_APPID" > /nest-dashboard/app/config/config.yml
    /nest-dashboard/app/console fetch
    sleep 30
done
