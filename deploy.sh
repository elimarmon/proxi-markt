#!/bin/bash
set -e

# 1. Sincronizar código
echo "==> Actualizando código desde Git..."
git fetch origin
git reset --hard origin/despliegue

# 2. Configuración de Nginx (SPA + API)
cat << 'EOF' > docker/nginx/default.conf
server {
    listen 80;
    root /var/www/html/frontend/dist; 
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~ ^/(api|auth|login|logout|register|sanctum|_debugbar) {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass backend:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME /var/www/html/backend/public/index.php;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
}
EOF

# 3. Reiniciar contenedores
cd produccion
echo "==> Reiniciando contenedores..."
docker-compose down
docker-compose up -d --build

# 4. CONTROL DE TIMING: Esperar a MySQL
echo "==> Esperando a que MySQL acepte conexiones..."
MAX_RETRIES=30
COUNT=0

# Intentamos hacer un ping a MySQL desde el contenedor de PHP
# para asegurar que la RED y el USUARIO funcionan.
until docker exec proximarkt-backend php -r "
    try {
        new PDO('mysql:host=mysql;dbname=proxi_markt', 'alumno', 'alumno');
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" > /dev/null 2>&1; do
    COUNT=$((COUNT+1))
    if [ $COUNT -ge $MAX_RETRIES ]; then
        echo "Error: MySQL no respondió tras 30 segundos. Revisa los logs de proximarkt-mysql."
        exit 1
    fi
    echo "MySQL sigue arrancando... (Intento $COUNT/$MAX_RETRIES)"
    sleep 2
done

echo "==> ¡MySQL conectado con éxito!"

# 5. Limpieza profunda y Migraciones
echo "==> Preparando Laravel..."
docker exec proximarkt-backend php artisan optimize:clear
docker exec proximarkt-backend php artisan migrate --force

echo "==> Despliegue completado con éxito."