FROM wdst-ocp-drupal-base:1.0
COPY src /code
RUN chmod -R g+rwX /code
RUN cd /code && rm -rf .git && composer install
