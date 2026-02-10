#!/bin/bash

# 1. Actualizar código
echo "📥 Actualizando repositorio..."
git pull origin main

# 2. Levantar Infraestructura (Traefik)
echo "🌐 Configurando Traefik..."
cd traefik
docker-compose up -d
cd ..

# 3. Levantar Aplicación (Producción)
echo "🚀 Reiniciando aplicación..."
cd produccion
docker-compose down -v
docker-compose up -d --build

# 4. Comandos internos de Laravel
echo "🛠️ Ejecutando limpieza y mantenimiento en Backend..."
# Esperamos 5 segundos para que MySQL respire
sleep 5

docker exec proximarkt-backend php artisan migrate --force
docker exec proximarkt-backend php artisan storage:link
docker exec proximarkt-backend php artisan config:clear
docker exec proximarkt-backend php artisan route:clear

echo "✅ ¡Todo listo! Web operativa en https://proximarkt.francecentral.cloudapp.azure.com"