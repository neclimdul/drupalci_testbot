FROM       composer/composer
MAINTAINER drupalci

RUN echo "Starting" && \
  apt-get update && \
  apt-get install apt-transport-https && \
  apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D && \
  echo 'deb https://apt.dockerproject.org/repo debian-jessie main' > /etc/apt/sources.list.d/docker.list && \
  apt-get update && \
  apt-get install -y git docker-engine && \
  rm -rf /var/lib/apt/lists/*

WORKDIR /drupalci
COPY ./drupalci ./drupalci
COPY ./src  ./src
COPY ./configsets ./configsets
COPY ./composer.json ./composer.json
COPY ./composer.lock ./composer.lock

RUN composer install -q && ./drupalci config:load blank

ENV PATH /drupalci:$PATH

ENTRYPOINT []
CMD ["./drupalci", "--help"]
