ARG from_image

FROM $from_image

RUN apk add --no-cache bash \
        && curl --silent https://getcomposer.org/composer-stable.phar -o /usr/bin/composer && chmod a+x /usr/bin/composer
