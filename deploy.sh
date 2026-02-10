#!/bin/bash
set -e

echo "==> 📥 Sincronizando con GitHub..."
git fetch origin
git reset --hard origin/despliegue
git clean -fd -e traefik/letsencrypt/

echo "==> 🌐 Verificando red proxy_net..."
# Crea la red si no existe para evitar el error de 'external network not found'
docker network inspect proxy_net >/dev/null 2>&1 || docker network create proxy_net

echo "==> 🚀 Reiniciando Contenedores..."
cd produccion

# Limpieza total incluyendo contenedores antiguos (orphans) y volumen de base de datos (-v)
docker-compose down -v --remove-orphans
docker-compose up -d --build

echo "==> ⏳ Esperando 15 segundos a MySQL (initdb)..."
sleep 15

echo "==> 🛠️ Reparando permisos de Laravel..."
# Usamos las rutas directas del contenedor
docker exec proximarkt-backend chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker exec proximarkt-backend chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> 📋 Ejecutando comandos de Laravel..."
docker exec proximarkt-backend php artisan migrate --force
docker exec proximarkt-backend php artisan config:clear
docker exec proximarkt-backend php artisan storage:link || echo "Aviso: El link de storage ya existe"

echo "==> ✅ DESPLIEGUE FINALIZADO"
docker-compose ps