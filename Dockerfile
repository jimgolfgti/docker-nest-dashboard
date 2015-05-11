FROM ubuntu:trusty
MAINTAINER Joeri Verdeyen <info@jverdeyen.be>

RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get -yq install \
        curl \
        git \
        apache2 \
        libapache2-mod-php5 &&\
    rm -rf /var/lib/apt/lists/* && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    sed -i "s/variables_order.*/variables_order = \"EGPCS\"/g" /etc/php5/apache2/php.ini && \
    printf "#!/bin/bash\n chown www-data:www-data /app -R\n source /etc/apache2/envvars \n tail -F /var/log/apache2/* &\n exec apache2 -D FOREGROUND" > /run.sh && \
    chmod 755 /run.sh && \
    mkdir -p /app && rm -fr /var/www/html && ln -s /app /var/www/html

VOLUME app/:/app
WORKDIR /app
RUN composer install --no-dev --prefer-source
EXPOSE 80

CMD ["/run.sh"]