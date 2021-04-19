FROM php:7.4-fpm
MAINTAINER Thibaut Cholley <thibaut.cholley@omnilog.fr>

# Update package, enable php configuration
RUN apt-get update\
    && cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini \
    && sed -i 's/memory_limit = 128M/memory_limit = -1/' /usr/local/etc/php/php.ini \
    && sed -i 's/post_max_size = 8M/post_max_size = 500M/' /usr/local/etc/php/php.ini \
    && sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 500M/' /usr/local/etc/php/php.ini \
    && sed -i 's/max_execution_time = 30/max_execution_time = 300/' /usr/local/etc/php/php.ini \
    && sed -i 's/;date.timezone =/date.timezone = Europe\/Paris/' /usr/local/etc/php/php.ini \
    && sed -i 's/;ping.path =/ping.path =/' /usr/local/etc/php-fpm.d/www.conf

# Add xdebug apcu opcache
RUN pecl install xdebug apcu \
    && docker-php-ext-enable xdebug apcu \
    && docker-php-ext-install opcache

# Enable coverage mode
RUN echo 'xdebug.mode = coverage' |  tee -a  /usr/local/etc/php/php.ini

# Add Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN composer config --global github-oauth.github.com ecf2dfe7dd27d143857ee6cdea103bea05a0a603

# Install php dep : intl
RUN apt-get update && apt-get install -y libzip-dev zlib1g-dev libicu-dev g++\
    && docker-php-ext-configure intl\
    && docker-php-ext-install intl

# Install php dep : zip
RUN apt-get install -y libzip-dev zlib1g-dev \
    && docker-php-ext-install zip

# Install php dep : sockets
RUN docker-php-ext-install sockets

# Install php dep : redis
RUN pecl install redis \
  && docker-php-ext-enable redis

# Install php dep : yaml
RUN apt install -y libyaml-dev \
    && pecl install yaml \
    && docker-php-ext-enable yaml

# Install php dep : amqp
RUN apt-get install -y \
        librabbitmq-dev \
        libssh-dev \
    && pecl install amqp \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable amqp

# Install Blackfire & CLI
RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version \
    && mkdir -p /tmp/blackfire \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get ('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
    && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz

RUN mkdir -p /tmp/blackfire \
    && curl -A "Docker" -L https://blackfire.io/api/v1/releases/client/linux_static/amd64 | tar zxp -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire /usr/bin/blackfire \
    && rm -Rf /tmp/blackfire

# Install Git
RUN apt-get install -y git

# Create dev user for specified uid:gid
ARG USER_ID=1000
ARG GROUP_ID=1000

RUN groupadd devdocker -g ${GROUP_ID} \
    && useradd -m -u ${USER_ID} -g ${GROUP_ID} dev

USER dev

RUN composer global require friendsofphp/php-cs-fixer phpstan/phpstan

ENV PATH "$PATH:~/.composer/vendor/bin"