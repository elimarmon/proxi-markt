#!/bin/bash

echo "📥 1. Actualizando código desde Git..."
# Guardamos cambios del .env local para que no den error al hacer pull
git pull 

echo "🌐 2. Asegurando infraestructura (Traefik)..."
cd traefik
docker-compose up -d
cd ..

echo "🚀 3. Reiniciando Aplicación (Producción)..."
cd produccion
# Usamos -v para limpiar volúmenes antiguos y asegurar que mysql cree el usuario 'alumno'
docker-compose down -v
docker-compose up -d --build

echo "⏳ Esperando a que los servicios despierten..."
sleep 8

echo "🛠️ 4. Ejecutando comandos internos de Laravel..."
# Reparar permisos primero para que los siguientes comandos no fallen
docker exec proximarkt-backend chown -R www-data:www-data storage bootstrap/cache
docker exec proximarkt-backend chmod -R 775 storage bootstrap/cache

# Ejecutar migraciones y links
docker exec proximarkt-backend php artisan migrate --force
docker exec proximarkt-backend php artisan storage:link
docker exec proximarkt-backend php artisan config:clear

echo "✅ ¡Despliegue completado con éxito!"