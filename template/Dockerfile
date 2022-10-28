FROM wyveo/nginx-php-fpm:php81

WORKDIR /usr/share/nginx/html
EXPOSE 80

# Set timezone
ENV TZ="Europe/Prague"

# Copy project files, Nginx configs & PHP configs
RUN rm -rf ./*
COPY . /usr/share/nginx/html
COPY ./docker/nginx /etc/nginx
COPY ./docker/php/8.1/cli/php.ini /etc/php/8.1/cli/php.ini
COPY ./docker/php/8.1/fpm/php.ini /etc/php/8.1/fpm/php.ini
COPY ./docker/php/8.1/fpm/php.ini /etc/php/8.1/fpm/php-fpm.conf
COPY ./docker/php/8.1/fpm/pool.d/www.conf /etc/php/8.1/fpm/pool.d/www.conf

# Permissions
RUN chmod -R ugo+w ./temp
RUN chmod -R ugo+w ./log

# Install and build dependencies
RUN composer install --prefer-dist --no-scripts && rm -rf /root/.composer

# Remove docker folder
RUN rm -rf ./docker

ADD docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

# Resolve permissions
RUN chown -R www-data:www-data /usr/share/nginx/html

# Set entrypoint file
ENTRYPOINT ["/docker-entrypoint.sh"]