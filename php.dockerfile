ARG IMAGE_PHP_VERSION=${IMAGE_PHP_VERSION}
FROM php:${IMAGE_PHP_VERSION:-8.0}-fpm-alpine
RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-enable pdo pdo_mysql
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN mv composer.phar /usr/local/bin/composer

RUN apk add git
RUN git config --global user.email "${GITHUB_USER}"

RUN wget https://github.com/symfony-cli/symfony-cli/releases/download/v5.4.1/symfony-cli_5.4.1_x86_64.apk
RUN apk add --allow-untrusted symfony-cli_5.4.1_x86_64.apk
RUN rm symfony-cli_5.4.1_x86_64.apk
