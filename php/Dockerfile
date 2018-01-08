FROM php:5.6-apache

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        libicu-dev \
        libxml2-dev \
        vim \
        wget \
        unzip \
        git \
    && docker-php-ext-install exif \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install -j$(nproc) iconv intl xml soap mcrypt opcache pdo pdo_mysql mysql mysqli mbstring \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/

RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.file_link_format=phpstorm://open?%f:%l" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.profiler_output_dir=/var/www/html/logs/" >> /usr/local/etc/php/conf.d/xdebug.ini

ADD php/sites-enabled/vhost.conf /etc/apache2/sites-enabled/

RUN a2enmod rewrite

COPY /php/php.ini /usr/local/etc/php/

COPY www /var/www

RUN a2ensite 000-default

RUN apache2ctl restart

RUN chmod 777 /tmp
RUN chmod 777 /var/log
