#!/bin/bash
set -e

# 1. Sincronizar
git fetch origin
git reset --hard origin/despliegue
echo "traefik/" > .dockerignore

# 2. CORRECCIÓN DE NGINX (Esto evita el error de Restarting)
# Escribimos el archivo directamente para que el reset no nos lo pise
cat << 'EOF' > docker/nginx/default.conf
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        # Usamos el nombre del servicio definido en el docker-compose
        fastcgi_pass backend:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
}
EOF

# 3. Reiniciar
cd produccion
docker-compose down -v --remove-orphans
docker-compose up -d --build

# 4. Espera
echo "==> Esperando 30s..."
sleep 30

# 5. Laravel
echo "==> Configurando Laravel..."
docker exec proximarkt-backend composer install --no-dev --optimize-autoloader
docker exec proximarkt-backend php artisan config:clear
docker exec proximarkt-backend php artisan route:clear
docker exec proximarkt-backend chown -R www-data:www-data storage bootstrap/cache
docker exec proximarkt-backend chmod -R 775 storage bootstrap/cache
docker exec proximarkt-backend php artisan migrate --force