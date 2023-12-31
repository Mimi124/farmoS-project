# Inherit from the Drupal 9 image on Docker Hub.
FROM drupal:9.5

# Set the farmOS and composer project repository URLs and versions.
ARG FARMOS_REPO=https://github.com/farmOS/farmOS.git
ARG FARMOS_VERSION=2.x
ARG PROJECT_REPO=https://github.com/farmOS/composer-project.git
ARG PROJECT_VERSION=2.x

# Set Apache ServerName directive globally to suppress AH00558 message.
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Install the BCMath PHP extension.
RUN docker-php-ext-install bcmath

# Install git and unzip (needed to build PHP GEOS and needed by Composer).
RUN apt-get update \
  && apt-get install -y git unzip

# Build and install the GEOS PHP extension.
# See https://git.osgeo.org/gitea/geos/php-geos
RUN apt-get update && apt-get install -y libgeos-dev \
  && git clone https://github.com/libgeos/php-geos.git \
  && ( \
    cd php-geos \
    # Checkout latest commit with PHP 8 support.
    && git checkout e77d5a16abbf89a59d947d1fe49381a944762c9d \
    && ./autogen.sh \
    && ./configure \
    && make \
    && make install \
  ) \
  && rm -r php-geos \
  && docker-php-ext-enable geos

# Set recommended PHP settings for farmOS.
# See https://farmos.org/hosting/installing/#requirements
RUN { \
    echo 'memory_limit=256M'; \
    echo 'max_execution_time=240'; \
    echo 'max_input_time=240'; \
    echo 'max_input_vars=5000'; \
    echo 'post_max_size=100M'; \
    echo 'upload_max_filesize=100M'; \
    echo 'expose_php=Off'; \
  } > /usr/local/etc/php/conf.d/farmOS-recommended.ini

# Set recommended realpath_cache settings.
# See https://www.drupal.org/docs/7/managing-site-performance/tuning-phpini-for-drupal
RUN { \
    echo 'realpath_cache_size=4096K'; \
    echo 'realpath_cache_ttl=3600'; \
  } > /usr/local/etc/php/conf.d/realpath_cache-recommended.ini

# Set recommended OPcache for maximum performance in Symfony applications.
# See https://symfony.com/doc/current/performance.html#configure-opcache-for-maximum-performance
# @todo
# Remove this when https://github.com/docker-library/drupal/pull/156 is merged.
RUN sed -i 's|opcache.memory_consumption=128|opcache.memory_consumption=256|g' /usr/local/etc/php/conf.d/opcache-recommended.ini \
  && sed -i 's|opcache.max_accelerated_files=4000|opcache.max_accelerated_files=20000|g' /usr/local/etc/php/conf.d/opcache-recommended.ini

# Install postgresql-client so Drush can connect to the database.
RUN apt-get update \
  # See https://stackoverflow.com/questions/51033689/how-to-fix-error-on-postgres-install-ubuntu
  && mkdir -p /usr/share/man/man1 \
  && mkdir -p /usr/share/man/man7 \
  && apt-get install -y postgresql-client

# Set the COMPOSER_MEMORY_LIMIT environment variable to unlimited.
ENV COMPOSER_MEMORY_LIMIT=-1

# Add the build-farmOS.sh script.
COPY build-farmOS.sh /usr/local/bin/
RUN chmod a+x /usr/local/bin/build-farmOS.sh

# Build the farmOS codebase in /var/farmoS with the --no-dev flag.
# Change the ownership of the sites directory and copy the farmOS codebase into /opt/drupal.
RUN mkdir /var/farmOS \
  && /usr/local/bin/build-farmOS.sh --no-dev \
  && chown -R www-data:www-data /var/farmOS/web/sites \
  && rm -r /opt/drupal && cp -rp /var/farmOS /opt/drupal

# Set the entrypoint.
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod u+x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
