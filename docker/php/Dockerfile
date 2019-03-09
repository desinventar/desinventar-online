FROM php:fpm
RUN apt-get update -y && \
  apt-get install -y \
    libfreetype6-dev \
    libjpeg-dev \
    zlib1g-dev \
    libpng-dev \
    libzip-dev \
    fonts-liberation
RUN docker-php-ext-configure gd \
  --with-jpeg-dir=/usr/include \
  --with-zlib-dir=/usr \
  --with-png-dir=/usr/include \
  --with-freetype-dir=/usr/include/freetype2
RUN docker-php-ext-install gd
RUN docker-php-ext-install zip
RUN apt-get update && apt-get install -y unzip git
COPY files/php-fpm/php.ini /usr/local/etc/php/php.ini

WORKDIR /opt/app
# Install composer to handle dependencies
RUN curl --silent --show-error https://getcomposer.org/installer > /tmp/composer-setup.php && \
  php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot

ADD composer.json /tmp/composer.json
ADD composer.lock /tmp/composer.lock

RUN cd /tmp && composer install --no-scripts --no-autoloader --no-interaction

COPY . /opt/app
RUN cp -a /tmp/vendor /opt/app && composer install
