FROM       drupalci/base
MAINTAINER drupalci

##
# Base
##

ENV DEBIAN_FRONTEND noninteractive
ENV HOME /root

# Saves us from stale repository issues.
RUN apt-get clean && apt-get update

# Build packages.
# (HHVM stuff goes here)
#
RUN apt-get install -y software-properties-common \
      curl \
      git \
      mysql-client \
      sqlite3


RUN apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0x5a16e7281be7a449
RUN add-apt-repository -y 'deb http://dl.hhvm.com/ubuntu trusty main'
RUN apt-get update -y
RUN apt-get install -y hhvm

RUN apt-get install -y apache2 apache2-mpm-worker apache2-dev apache2-utils libapache2-mod-fastcgi
RUN a2dismod mpm_event && a2enmod mpm_worker alias actions rewrite fastcgi

RUN apt-get clean && apt-get autoremove -y

##
# Composer.
##

RUN bash -c "wget http://getcomposer.org/composer.phar && chmod 775 composer.phar && sudo mv composer.phar /usr/local/bin/composer"

# Drush and dependencies.
RUN HOME=/ /usr/local/bin/composer global require drush/drush:dev-master
RUN /.composer/vendor/drush/drush/drush --version

# supervisor
COPY ./conf/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Scripts.
COPY ./conf/scripts/start.sh /start.sh
COPY ./conf/scripts/foreground.sh /etc/apache2/foreground.sh
COPY daemon.sh /daemon.sh

COPY ./conf/apache2/vhost.conf /etc/apache2/sites-available/drupal.conf
RUN a2dissite 000-default.conf
RUN a2ensite drupal.conf

COPY ./conf/hhvm/server.ini /etc/hhvm/server.ini
COPY ./conf/hhvm/php.ini /etc/hhvm/php.ini

RUN service hhvm restart

# Make start.sh executable.
RUN chmod 755 /start.sh /daemon.sh
RUN chown -R www-data:www-data /var/www
RUN chmod +wx /var/www

EXPOSE 80
CMD ["/bin/bash", "/start.sh"]
