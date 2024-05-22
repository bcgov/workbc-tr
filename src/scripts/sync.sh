#! /bin/bash
set -e
composer install
drush cr
drush updb -y
drush cim
drush deploy:hook -y
drush cr
