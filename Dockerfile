FROM docker.arvancloud.ir/dunglas/frankenphp:php8.3

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    libzip-dev \
    unzip \
    git \
    libonig-dev \
    curl \
    supervisor \
    libpq-dev \
    ffmpeg \
    cron

RUN ffmpeg -version

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pgsql pdo_pgsql mbstring zip exif pcntl gd sockets ftp

WORKDIR /home/app

RUN (crontab -l ; echo "* * * * * cd /home/app && /usr/local/bin/php artisan schedule:run >> /var/log/cron-1.log 2>&1") | crontab

COPY . .

COPY ./php.ini /usr/local/etc/php
COPY ./supervisor.conf /etc/supervisor/conf.d

RUN chown -R www-data:www-data \
        /home/app/storage \
        /home/app/bootstrap/cache

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --ignore-platform-reqs

EXPOSE 8000

COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod a+x /usr/local/bin/entrypoint.sh

ENTRYPOINT /usr/local/bin/entrypoint.sh
