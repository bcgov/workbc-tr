FROM wdst-ocp-drupal-base:1.0
COPY src /code
USER 1001
RUN cd /code && rm -rf .git && composer install
