#FROM wdst-ocp-drupal-base:1.0
FROM 075458558257.dkr.ecr.ca-central-1.amazonaws.com/drupal-base:6.0
COPY --chown=1001:1 src /code
#USER 1001
#RUN chmod -R g+rwX /code
RUN cd /code && rm -rf .git && composer install && COMPOSER_MEMORY_LIMIT=-1 composer update
