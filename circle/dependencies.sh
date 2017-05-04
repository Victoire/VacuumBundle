#!/usr/bin/env bash

echo "memory_limit = 2048M" > /opt/circleci/php/$(phpenv global)/etc/conf.d/memory.ini
echo "always_populate_raw_post_data=-1" > /opt/circleci/php/$(phpenv global)/etc/conf.d/post_data.ini
if [ -n "${RUN_NIGHTLY_BUILD}" ]; then
  sed -i 's/^;//' /opt/circleci/php/$(phpenv global)/etc/conf.d/xdebug.ini
  echo "xdebug enabled"
fi

# Add libxml2 installation to avoid html wrapper issue
# when using saveHtml method on article content
wget ftp://xmlsoft.org/libxml2/LATEST_LIBXML2
tar -zxvf LATEST_LIBXML2
cd libxml2-2.9.4
./configure --prefix=/usr/local/libxml2
make install

php -d memory_limit=-1 /usr/local/bin/composer install --prefer-dist