# Etapa 1 - PHP + extensões
FROM php:8.2-fpm

# Instala dependências do sistema e extensões PHP
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

# Etapa 2 - Composer
FROM composer:2.6 AS composer_stage

# Etapa final
FROM php:8.2-fpm

# Define diretório de trabalho
WORKDIR /var/www

# Copia arquivos da aplicação
COPY . .

# Copia o Composer da imagem anterior
COPY --from=composer_stage /usr/bin/composer /usr/bin/composer

# Corrige permissões para evitar erros
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Instala dependências PHP
RUN composer install

# Copia arquivos do Supervisor
COPY supervisor/worker.conf /etc/supervisor/conf.d/worker.conf
COPY supervisor/php-fpm.conf /etc/supervisor/conf.d/php-fpm.conf
COPY supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Copia o script de entrada
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expõe porta do PHP-FPM
EXPOSE 9000

# Define entrypoint
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
