#!/bin/bash
# Script para gerar cobertura de código

# Verificar se Xdebug está habilitado
if ! docker compose exec app php -m 2>/dev/null | grep -q xdebug; then
    echo "Habilitando Xdebug..."
    docker compose exec --user root app bash -c "echo 'zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20220829/xdebug.so' > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && echo 'xdebug.mode=coverage' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini"
    docker compose restart app
    sleep 2
fi

# Gerar cobertura
echo "Gerando relatório de cobertura..."
docker compose exec app vendor/bin/phpunit --coverage-text --coverage-html coverage/html

echo ""
echo "✅ Relatório gerado em: coverage/html/index.html"

