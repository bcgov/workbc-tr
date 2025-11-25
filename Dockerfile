#FROM 075458558257.dkr.ecr.ca-central-1.amazonaws.com/drupal-base:6.0
FROM 001674982960.dkr.ecr.ca-central-1.amazonaws.com/drupal-base:1.0
ARG GITHUB_SHA=unknown
ENV OPENSHIFT_BUILD_NAME=$GITHUB_SHA
ENV COMPOSER_ALLOW_SUPERUSER=1


COPY --chown=1001:1 src /code
#USER 1001
#RUN chmod -R g+rwX /code
RUN cd /code && rm -rf .git && composer install && COMPOSER_MEMORY_LIMIT=-1 composer update
ENV PATH="/app/vendor/bin:${PATH}"
