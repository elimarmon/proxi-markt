#!/bin/bash
set -e

# 1. Sincronizar código
git fetch origin
git reset --hard origin/despliegue

# 2. Asegurar que el build no lea traefik (el archivo manual que hicimos antes)
echo "traefik/" > .dockerignore

# 3. Reiniciar contenedores
cd produccion
docker-compose down -v --remove-orphans
docker-compose up -d --build

# 4. Configurar Laravel
echo "==> Instalando librerías y configurando..."
sleep 15

# ESTA ES LA LÍNEA CLAVE PARA ARREGLAR TU ERROR:
docker exec proximarkt-backend composer install --no-dev --optimize-autoloader

# Reparar permisos
docker exec proximarkt-backend chown -R www-data:www-data storage bootstrap/cache
docker exec proximarkt-backend chmod -R 775 storage bootstrap/cache

# Migraciones
docker exec proximarkt-backend php artisan migrate --force
docker exec proximarkt-backend php artisan config:clear