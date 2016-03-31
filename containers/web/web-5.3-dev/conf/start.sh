#!/bin/bash
sudo supervisord
/usr/sbin/apache2ctl -D FOREGROUND
