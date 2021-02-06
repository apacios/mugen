#!/bin/sh
set -e

# run last minute build tools just for local dev
# this file should just be used to override on local dev in a compose file

# run default entrypoint first
/usr/local/bin/docker-php-entrypoint

service cron start
exec "$@"
