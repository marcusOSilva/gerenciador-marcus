FROM php:8.2-fpm

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    libpq-dev \
    libssl-dev \
    rabbitmq-server \
    supervisor \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip sockets \
    && pecl install mongodb-1.19.1 \
    && docker-php-ext-enable mongodb

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho
WORKDIR /var/www

# Copia os arquivos da aplicação
COPY . .

# Permissões
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Instala dependências PHP
RUN composer install

# Expõe porta do PHP-FPM
EXPOSE 9000

# Copia arquivos do Supervisor

COPY supervisor/worker.conf /etc/supervisor/conf.d/worker.conf
COPY supervisor/php-fpm.conf /etc/supervisor/conf.d/php-fpm.conf
COPY supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Copia e define o entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
