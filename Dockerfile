FROM wdst-ocp-drupal-base:1.0
#USER 1001
COPY src /code
RUN chown -R 1001:0 /code && chmod -R g+rwX /code
RUN cd /code && rm -rf .git && composer install
