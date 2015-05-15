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

# Make start.sh executable.
RUN chmod 755 /start.sh /daemon.sh
