#!/bin/bash
set -e

if [ ! -f /var/www/html/index.php ]; then
    echo "MODX not found in volume, downloading version ${MODX_VERSION}..."
    wget -q "https://github.com/modxcms/revolution/archive/refs/tags/v${MODX_VERSION}.tar.gz" -O /tmp/modx.tar.gz
    tar -xzf /tmp/modx.tar.gz -C /tmp
    cp -a "/tmp/revolution-${MODX_VERSION}/." /var/www/html/
    rm -rf /tmp/modx.tar.gz "/tmp/revolution-${MODX_VERSION}"

    echo "Installing composer dependencies (xpdo, smarty, etc.)..."
    composer install --no-dev --no-interaction --optimize-autoloader --working-dir=/var/www/html

    chown -R www-data:www-data /var/www/html
    echo "MODX ${MODX_VERSION} source ready. Open http://localhost:8080/setup/ to install."
fi

mkdir -p /var/www/html/core/components/customform
mkdir -p /var/www/html/assets/components/customform
chown -R www-data:www-data /var/www/html/core/components/customform /var/www/html/assets/components/customform

exec "$@"
