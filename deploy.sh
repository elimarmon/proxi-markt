#!/bin/bash

# Salir inmediatamente si un comando falla
set -e

echo "📥 1. Sincronizando con la rama despliegue..."
# Traemos info de la nube y forzamos que el local sea igual
git pull

echo "🌐 2. Asegurando infraestructura (Traefik)..."
cd traefik
docker compose up -d
cd ..

echo "🚀 3. Reiniciando Aplicación (Producción)..."
cd produccion
# -v es CRUCIAL para que MySQL cree de nuevo el usuario 'alumno' con la pass correcta
docker compose down -v
docker compose up -d --build

echo "⏳ Esperando 10 segundos a que la base de datos esté lista..."
sleep 10

echo "🛠️ 4. Reparando permisos de carpetas (Solución al error de logs)..."
# Esto soluciona el Permission Denied de la imagen
docker exec proximarkt-backend chown -R www-data:www-data storage bootstrap/cache
docker exec proximarkt-backend chmod -R 775 storage bootstrap/cache

echo "📋 5. Ejecutando comandos de Laravel..."
docker exec proximarkt-backend php artisan migrate --force
docker exec proximarkt-backend php artisan storage:link
docker exec proximarkt-backend php artisan config:clear
docker exec proximarkt-backend php artisan route:clear

echo "✅ ¡Despliegue completado con éxito!"