#!/bin/bash
set -e

# 1. Sincronizar
git fetch origin
git reset --hard origin/despliegue
echo "traefik/" > .dockerignore

# 2. Reiniciar
cd produccion
docker-compose down -v --remove-orphans
docker-compose up -d --build

# 3. Espera CRÍTICA
echo "==> Esperando a que MySQL esté totalmente listo (30s)..."
# MySQL en frío con un .sql de carga tarda más de 15s
sleep 30

# 4. Instalación y Limpieza
echo "==> Instalando dependencias y limpiando caché..."
docker exec proximarkt-backend composer install --no-dev --optimize-autoloader

# Forzamos la regeneración de la clave y limpieza de rutas
docker exec proximarkt-backend php artisan key:generate --force || true
docker exec proximarkt-backend php artisan config:cache
docker exec proximarkt-backend php artisan route:cache
docker exec proximarkt-backend php artisan view:cache

# 5. Permisos
docker exec proximarkt-backend chown -R www-data:www-data storage bootstrap/cache
docker exec proximarkt-backend chmod -R 775 storage bootstrap/cache

# 6. Migraciones (Ahora MySQL debería responder)
echo "==> Ejecutando migraciones..."
docker exec proximarkt-backend php artisan migrate --force