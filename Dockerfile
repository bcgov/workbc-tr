FROM wdst-ocp-drupal-base:1.0
COPY --chown=1001:1 src /code
#RUN chmod -R g+rwX /code
RUN cd /code && rm -rf .git && composer install && COMPOSER_MEMORY_LIMIT=-1 composer update
USER 1001
