#!/bin/bash

# Corrige permissões para garantir que Laravel possa gravar em storage e cache
echo "Ajustando permissões para storage e bootstrap/cache..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Verifica se o diretório vendor não existe e executa o composer install
if [ ! -d "/var/www/vendor" ]; then
    echo "Instalando dependências com Composer..."
    composer install --no-interaction --prefer-dist
fi

# Inicia o supervisord
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
