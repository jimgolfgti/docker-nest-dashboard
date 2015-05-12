FROM ubuntu:trusty
MAINTAINER Joeri Verdeyen <info@jverdeyen.be>

ENV NEST_USERNAME test@test.be
ENV NEW_PASSWORD password

RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get -yq install \
        curl \
        git \
        php5-cli \
        php5-curl &&\
    rm -rf /var/lib/apt/lists/* && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./nest-dashboard/ /nest-dashboard

WORKDIR /nest-dashboard

ADD run.sh /run.sh
RUN chmod +x /run.sh

RUN composer install --no-dev -o -n
EXPOSE 80

CMD ["/run.sh"]