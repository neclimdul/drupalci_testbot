FROM       php:5.6-cli
MAINTAINER drupalci

RUN apt-get update && apt-get install -y git \
  && rm -rf /var/lib/apt/lists/*
COPY ./ ./

CMD ["./drupalci"]

