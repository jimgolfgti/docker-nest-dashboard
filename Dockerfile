FROM php:5.6-cli-alpine

ENV NEST_USERNAME=test@test.be \
    NEST_PASSWORD=password \
    OPENWEATHERMAP_CITYID=5128581 \
    OPENWEATHERMAP_APPID=abc123 \
    DEFAULT_TIMEZONE=UTC

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY nest-dashboard/composer.* /nest-dashboard/
WORKDIR /nest-dashboard
RUN composer install --no-dev -o -n

COPY nest-dashboard/ /nest-dashboard

COPY entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
