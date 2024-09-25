FROM php:8.3-fpm-alpine

WORKDIR /var/www

ARG TARGETARCH

RUN apk --update add wget \
  curl \
  git \
  grep \
  build-base \
  libmcrypt-dev \
  libxml2-dev \
  imagemagick-dev \
  pcre-dev \
  libtool \
  make \
  autoconf \
  g++ \
  cyrus-sasl-dev \
  libgsasl-dev \
  oniguruma-dev \
  postgresql-dev \
  libzip-dev \
  nginx

RUN docker-php-ext-install mbstring \
  pdo_pgsql \
  xml \
  gd \
  bcmath \
  zip

COPY . /var/www
COPY ./infra/nginx.conf /etc/nginx/nginx.conf
COPY ./infra/php.ini /usr/local/etc/php/conf.d/zz-extra.ini
COPY ./infra/php.conf /usr/local/etc/php-fpm.d/zz-extra.conf

COPY --chown=www-data:www-data . /var/www
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8080

RUN ["chmod", "+x", "startup.sh"]

CMD sh /var/www/startup.sh
