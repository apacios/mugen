#--- Base build
FROM php:7.4-apache as base

RUN apt-get update && apt-get install -y \
  git \
  zip \
  libicu-dev \
  libzip-dev  \
  libpng-dev \
  libjpeg-dev \
  libfreetype6-dev \
  ffmpeg \
  cron

RUN apt remove yarn
RUN docker-php-ext-configure zip
RUN docker-php-ext-install gd pdo pdo_mysql zip opcache
RUN docker-php-ext-enable opcache
# Install gd for liip_imagine
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd

COPY --from=composer:1.8.6 /usr/bin/composer /usr/bin/composer
RUN composer global require hirak/prestissimo

#--- Dev build
FROM base as dev

# NodeJS / NPM
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - \
  && apt-get install -y nodejs \
  && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
  && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
  && apt update \
  && apt install yarn \
  && apt install -y nano

RUN yes | pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

COPY docker/app/app.conf /etc/apache2/sites-available/000-default.conf
COPY docker/app/app.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/app/.bashrc /root/.bashrc

COPY docker/app/crontab /etc/cron.d/crontab
RUN chmod 0644 /etc/cron.d/crontab
RUN crontab /etc/cron.d/crontab
RUN touch /var/log/cron.log
RUN service cron start

COPY docker/app/entrypoint.sh /
RUN chmod +x /entrypoint.sh

CMD ["apache2-foreground"]
ENTRYPOINT ["/entrypoint.sh"]

#--- Production build
FROM base as prod

COPY docker/app/prod-app.conf /etc/apache2/sites-available/000-default.conf
COPY docker/app/prod-app.ini /usr/local/etc/php/conf.d/app.ini
COPY . /var/www/html
COPY docker/app/.env.local .env.local
RUN composer install --no-interaction --optimize-autoloader
RUN chown -R www-data:www-data /var/www/html/var
RUN chmod -R ug+rwX var/

CMD ["apache2-foreground"]
ENTRYPOINT ["docker-php-entrypoint"]
