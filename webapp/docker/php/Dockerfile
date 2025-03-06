ARG ASSETS_BUILD=assets
FROM $ASSETS_BUILD AS assets-build

FROM php:8.4-fpm-bookworm AS php_base

USER root

RUN apt-get update -q -y \
    && apt-get upgrade -q -y \
    && curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash - \
    && apt-get install -q -y postgresql-client libcurl4-gnutls-dev zlib1g-dev libicu-dev g++ libxml2-dev libpq-dev \
      libonig-dev libzip-dev libldb-dev libldap2-dev libfreetype-dev libjpeg62-turbo-dev libpng-dev libxrender-dev \
      librabbitmq-dev software-properties-common git git-lfs unzip procps locales build-essential \
      libmagickwand-dev libmagickcore-dev imagemagick ghostscript fcgiwrap symfony-cli \
    && apt-get autoremove && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-source extract \
    && pecl install redis \
    && pecl install amqp \
    && docker-php-ext-enable redis amqp \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install intl mbstring pdo pdo_pgsql pdo_mysql zip bcmath sockets ldap opcache pcntl soap \
    && docker-php-source delete

COPY docker/php/conf.d/docker-common.ini $PHP_INI_DIR/conf.d/docker-common.ini
COPY docker/php/php-fpm.d/zz-docker-common.conf /usr/local/etc/php-fpm.d/zz-docker-common.conf

WORKDIR /app

COPY .env \
    composer.json \
    composer.lock \
    symfony.lock \
    ./

COPY docker/php/symfony-app-entrypoint.sh /symfony-app-entrypoint.sh

ENTRYPOINT ["/bin/bash", "/symfony-app-entrypoint.sh"]
CMD ["php-fpm"]

FROM php_base AS php_installer

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

COPY assets assets
COPY bin bin
COPY config config
COPY migrations migrations
RUN mkdir public
COPY public/index.php public
COPY src src
COPY templates templates
COPY translations translations

RUN APP_ENV=prod composer install --optimize-autoloader --no-progress --no-interaction --no-dev

FROM php_installer AS php_prod

COPY docker/php/conf.d/docker-prod.ini $PHP_INI_DIR/conf.d/docker-prod.ini
COPY docker/php/php-fpm.d/zz-docker-prod.conf /usr/local/etc/php-fpm.d/zz-docker-prod.conf

RUN APP_ENV=prod composer run-script post-install-cmd \
    && APP_ENV=prod composer dump-autoload --no-dev --classmap-authoritative \
    && mkdir -p var \
    && chown -R www-data:www-data var/ \
    && chown -R www-data:www-data public/

COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck

RUN chmod +x /usr/local/bin/docker-healthcheck

USER www-data

COPY --from=assets-build /app/public/build/entrypoints.json /app/public/build/entrypoints.json
COPY --from=assets-build /app/public/build/manifest.json /app/public/build/manifest.json
COPY --from=assets-build /app/var/cache/fosRoutes.json /app/var/cache/fosRoutes.json

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

FROM php_base AS php_dev

ENV COMPOSER_MEMORY_LIMIT=-1

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY docker/php/conf.d/docker-dev.ini $PHP_INI_DIR/conf.d/docker-dev.ini

RUN mkdir -p var /.composer /.symfony && chown -R www-data:www-data var/ /.composer/ /.symfony/
USER www-data
