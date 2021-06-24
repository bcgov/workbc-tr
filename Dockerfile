FROM wdst-ocp-drupal-base:1.0
USER 1001
COPY src /code
#RUN cd /code && rm -rf .git && composer install
