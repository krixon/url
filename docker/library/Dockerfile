FROM php:7.2-cli-alpine

ARG UID

# Create a user account manually rather than using:
# adduser -D -u $UID -h /home/dev dev
# This is necessary to support $UIDs > 256000.
RUN echo "dev:x:$UID:$UID::/home/dev:" >> /etc/passwd \
    && echo "dev:!:$(($(date +%s) / 60 / 60 / 24)):0:99999:7:::" >> /etc/shadow \
    && echo "dev:x:$UID:" >> /etc/group \
    && mkdir /home/dev && chown dev: /home/dev

RUN apk add --no-cache git openssh-client openssh autoconf g++ make

# install xdebug for code coverage analysis.
RUN pecl install xdebug && docker-php-ext-enable xdebug

# remove development packages
RUN apk del \
    g++ \
    make \
    autoconf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

USER dev

WORKDIR /src