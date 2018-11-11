FROM php:7.2-cli-stretch

# Install librdkafka1, dependency of the PHP extension
RUN apt-get update ;\
    apt-get install -y --no-install-recommends librdkafka1 librdkafka-dev unzip ;\
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false ;\
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pcntl

# Install PHP extension
RUN pecl install rdkafka
COPY rdkafka.ini /usr/local/etc/php/conf.d/

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
ENV COMPOSER_VERSION 1.7.3
RUN curl --silent --fail --location --retry 3 --output /tmp/installer.php --url https://raw.githubusercontent.com/composer/getcomposer.org/b107d959a5924af895807021fcef4ffec5a76aa9/web/installer \
 && php -r " \
    \$signature = '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061'; \
    \$hash = hash('SHA384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
        unlink('/tmp/installer.php'); \
        echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
        exit(1); \
    }" \
 && php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION} \
 && composer --ansi --version --no-interaction \
 && rm -rf /tmp/* /tmp/.htaccess

WORKDIR /srv/app
