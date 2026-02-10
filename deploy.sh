#!/bin/bash
set -e

echo "==> 📥 Sincronizando con GitHub (Forzado)..."
git fetch origin
git reset --hard origin/despliegue
git clean -fd -e traefik/letsencrypt/

echo "==> 🚀 Reiniciando Contenedores..."
cd produccion
# Usamos -v para que MySQL lea el base.sql de nuevo desde cero
docker-compose down -v
docker-compose up -d --build

echo "==> ⏳ Esperando a MySQL (15s)..."
sleep 15

echo "==> 🛠️ Arreglando permisos y Laravel..."
docker exec proximarkt-backend chown -R www-data:www-data storage bootstrap/cache
docker exec proximarkt-backend chmod -R 775 storage bootstrap/cache
docker exec proximarkt-backend php artisan migrate --force
docker exec proximarkt-backend php artisan config:clear

echo "==> ✅ ¡Despliegue terminado!"