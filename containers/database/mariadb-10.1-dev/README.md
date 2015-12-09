containers/database/mariadb-10.0
====

### Overview

This container is responsible for providing a mariadb 10.1 service container,
for jobs which require a mariadb-10.1 database environment.

### Usage

To obtain the mariadb-10.1 image for use on your local environment, select the
mariadb-10.1 container during the database selection step inside of the
'drupalci init' command.  To obtain the image without re-running drupalci init,
any of the following methods may be used:
- drupalci init:database (and select the mariadb-10.1 option)
  or
- drupalci pull drupalci/mariadb-10.1
  or
- docker pull drupalci/mariadb-10.1

If building the container images locally, this container depends on the
existence of the 'drupalci/db-base' container image before it can be built.
