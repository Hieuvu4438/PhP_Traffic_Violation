FROM php:8.2-apache

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql

# Enable mod_rewrite for .htaccess
RUN a2enmod rewrite

# Set DocumentRoot to /var/www/html/public and allow .htaccess overrides
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Force UTF-8 charset for all text content
RUN printf '\nAddDefaultCharset UTF-8\nAddCharset UTF-8 .css .js\n' >> /etc/apache2/apache2.conf

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy project files
COPY . /var/www/html/

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
