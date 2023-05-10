#! /bin/bash
set -e
composer install
drush cr
drush updb -y --no-post-updates
drush cim
drush updb -y
drush cr
