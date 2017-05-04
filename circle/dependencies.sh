#!/usr/bin/env bash

echo "memory_limit = 2048M" > /opt/circleci/php/$(phpenv global)/etc/conf.d/memory.ini
echo "always_populate_raw_post_data=-1" > /opt/circleci/php/$(phpenv global)/etc/conf.d/post_data.ini
if [ -n "${RUN_NIGHTLY_BUILD}" ]; then
  sed -i 's/^;//' /opt/circleci/php/$(phpenv global)/etc/conf.d/xdebug.ini
  echo "xdebug enabled"
fi

dpkg -l | grep libxml2

php -d memory_limit=-1 /usr/local/bin/composer install --prefer-dist