<?php

// Allow long-running scripts
set_time_limit(0);

// Sets the socket timeout to a very long time so that
// Guzzle requests to docker have enough time to wait for output.
ini_set("default_socket_timeout", "1000");

