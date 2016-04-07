#!/bin/bash
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
/usr/sbin/apache2ctl -D FOREGROUND
