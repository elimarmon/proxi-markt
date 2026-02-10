#!/bin/bash
set -e

# 1. Sincronizar código
git fetch origin
git reset --hard origin/despliegue

# 2. Reiniciar contenedores
cd produccion
docker-compose down -v --remove-orphans
docker-compose up -d --build

# 3. Configurar Laravel (Esperamos a que MySQL arranque)
sleep 15
docker exec proximarkt-backend chown -R www-data:www-data storage bootstrap/cache
docker exec proximarkt-backend chmod -R 775 storage bootstrap/cache
docker exec proximarkt-backend php artisan migrate --force