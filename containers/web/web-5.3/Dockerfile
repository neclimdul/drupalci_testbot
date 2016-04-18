FROM       ubuntu:trusty
MAINTAINER drupalci

ENV DEBIAN_FRONTEND noninteractive
ENV HOME /root
ENV DRUPALCI TRUE
# PHP 5.3.29
ENV PHP_VERSION 5.3.29

RUN buildDeps=" \
    bison \
    build-essential \
    ccache \
    libaspell-dev \
    libbz2-dev \
    libc-client-dev \
    libcurl4-openssl-dev \
    libdb5.1-dev \
    libfreetype6-dev \
    libgmp3-dev \
    libicu-dev \
    libjpeg-dev \
    libldap2-dev \
    libmcrypt-dev \
    libmhash-dev \
    libmysqlclient-dev \
    libmysqlclient15-dev \
    libpcre3-dev \
    libpng-dev \
    libpq-dev \
    libreadline6-dev \
    librecode-dev \
    libsnmp-dev \
    libsqlite-dev \
    libssl-dev \
    libt1-dev \
    libtidy-dev \
    libxml2-dev \
    libxpm-dev \
    libxslt-dev \
    libyaml-dev \
    libz-dev \
    make \
    ncurses-dev \
    php5-cli \
    php5-dev \
    software-properties-common \
    unixODBC-dev \
    " \
    && set -x \
    && apt-get update \
    && apt-get install -y \
        apache2 apache2-mpm-prefork apache2-dev freetds-dev git libc6 \
        libgcc1 libmcrypt4 libfontconfig libgcrypt20 libicu52 libjpeg8 \
        libkeyutils1 libpng12-0 libreadline6 libtidy-0.99-0 libwebp5 \
        libxslt1.1 mysql-client re2c sqlite3 sudo zlib1g \
        --no-install-recommends \
    && apt-get install -y $buildDeps --no-install-recommends \
    && add-apt-repository -y ppa:n-muench/programs-ppa2 \
    && apt-get update \
    && apt-get install -y curl libcurl3-dev \
    && rm -rf /var/lib/apt/lists/* \
    && curl -o /usr/local/bin/composer http://getcomposer.org/composer.phar \
    && chmod 775 /usr/local/bin/composer \
    && HOME=/ /usr/bin/php /usr/local/bin/composer global require drush/drush:7.0.0 \
    && ln -s /.composer/vendor/drush/drush/drush /usr/local/bin/drush \
    && /.composer/vendor/drush/drush/drush --version \
    && mkdir /usr/include/freetype2/freetype \
    && ln -s /usr/include/freetype2/freetype.h /usr/include/freetype2/freetype/freetype.h \
    && gpg --keyserver pgp.mit.edu --recv-keys 0B96609E270F565C13292B24C13C70B87267B52D 0A95E9A026542D53835E3F3A7DEC4E69FC9C83D7 \
    && cd / \
    && curl -SL "http://us2.php.net/get/php-$PHP_VERSION.tar.bz2/from/this/mirror" -o php.tar.bz2 \
    && curl -SL "http://us2.php.net/get/php-$PHP_VERSION.tar.bz2.asc/from/this/mirror" -o php.tar.bz2.asc \
    && gpg --verify php.tar.bz2.asc \
    && mkdir -p /usr/src/php \
    && tar -xf php.tar.bz2 -C /usr/src/php --strip-components=1 \
    && rm php.tar.bz2* \
    && cd /usr/src/php \
    && ./configure \
    --with-apxs2=/usr/bin/apxs2 \
    --with-gd \
    --enable-sockets \
    --with-jpeg-dir=/usr \
    --with-png-dir=/usr \
    --enable-exif \
    --enable-zip \
    --with-zlib \
    --with-zlib-dir=/usr \
    --with-kerberos \
    --with-openssl \
    --with-mcrypt=/usr \
    --enable-soap \
    --enable-xmlreader \
    --with-xsl \
    --enable-ftp \
    --enable-cgi \
    --with-curl=/usr \
    --with-tidy \
    --with-xmlrpc \
    --enable-sysvsem \
    --enable-sysvshm \
    --enable-shmop \
    --with-mysql=mysqlnd \
    --with-mysqli=mysqlnd \
    --with-pdo-mysql=mysqlnd \
    --with-pdo-sqlite \
    --enable-pcntl \
    --with-readline \
    --enable-mbstring \
    --disable-debug \
    --enable-bcmath \
    --with-gettext=shared \
    --with-freetype-dir=/usr/lib/x86_64-linux-gnu \
    && make -j"$(nproc)" \
    && make install \
    && echo | pecl install apc \
    && echo | pecl install channel://pecl.php.net/xdebug-2.2.7 \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false -o APT::AutoRemove::SuggestsImportant=false $buildDeps

##
# Apache Configuration.
##
COPY ./conf/apache2/vhost.conf /etc/apache2/sites-available/drupal.conf
COPY ./conf/apache2/php5.conf /etc/apache2/mods-enabled/php5.conf
RUN a2dismod mpm_event \
    && a2enmod mpm_prefork \
    && a2enmod rewrite \
    && a2dissite 000-default.conf \
    && a2ensite drupal

##
# copying php.ini for compiled php
##
RUN mkdir -p /usr/local/etc/php
COPY ./conf/php/php.ini /usr/local/etc/php.ini

COPY ./conf/start.sh /usr/local/bin/start.sh
RUN chmod 775 /usr/local/bin/start.sh
