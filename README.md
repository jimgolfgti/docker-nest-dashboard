# A Nest thermostat dashboard, running on docker, built with PHP, InfluxDB and Grafana.

![](example.jpg)

## Getting started

* Install docker
* Create `.env` file to configure your Nest and OpenWeatherMap credentials
```sh
NEST_USERNAME=user@example.com
NEST_PASSWORD=somepassword
OPENWEATHERMAP_APPID=a1b2c3d4e5f6 # https://home.openweathermap.org/api_keys
OPENWEATHERMAP_CITYID=2643743     # https://openweathermap.org/city/2643743
```

## Build the stack

```
docker-compose up
```
