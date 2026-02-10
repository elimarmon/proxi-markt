#!/usr/bin/env bash
set -euo pipefail

# Script profesional para ProxiMarkt
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "==> 📥 Actualizando repositorio..."
cd "${ROOT_DIR}"
# Limpiamos cambios locales para evitar el error de 'merge' que viste antes
git fetch origin
git reset --hard origin/despliegue

echo "==> 🌐 Asegurando infraestructura (Traefik)..."
if [ -d "traefik" ]; then
    cd "traefik" && docker compose up -d && cd ..
fi

echo "==> 🚀 Entrando en produccion/ y levantando..."
cd "${ROOT_DIR}/produccion"

if [[ ! -f ".env" ]]; then
  echo "❌ ERROR: Falta produccion/.env"
  exit 1
fi

# Detección de comando Compose
COMPOSE_CMD="docker-compose"
if docker compose version >/dev/null 2>&1; then COMPOSE_CMD="docker compose"; fi

# Levantamos con -v para asegurar limpieza de DB si es necesario
${COMPOSE_CMD} down -v
${COMPOSE_CMD} up -d --build

echo "==> ⏳ Esperando a que el sistema respire (10s)..."
sleep 10

echo "==> 🛠️ Reparando permisos y ejecutando Laravel..."
# SOLUCIÓN AL ERROR DE TU IMAGEN
docker exec proximarkt-backend chown -R www-data:www-data storage bootstrap/cache
docker exec proximarkt-backend chmod -R 775 storage bootstrap/cache

# Comandos de mantenimiento
docker exec proximarkt-backend php artisan migrate --force
docker exec proximarkt-backend php artisan storage:link
docker exec proximarkt-backend php artisan config:clear

echo "==> ✅ OK: Despliegue terminado y permisos reparados"
${COMPOSE_CMD} ps