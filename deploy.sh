#!/bin/bash
set -e

# 1. Sincronizar (traerá la carpeta dist que acabas de subir)
git fetch origin
git reset --hard origin/despliegue

# 2. Configurar Nginx para que "vea" Vue y Laravel a la vez
cat << 'EOF' > docker/nginx/default.conf
server {
    listen 80;
    # IMPORTANTE: Ruta a los archivos que generó el build de Vue
    root /var/www/html/frontend/dist; 
    index index.html;

    location / {
        # Esto soluciona el 404 de /auth: redirige todo a index.html
        try_files $uri $uri/ /index.html;
    }

    # Redirigir peticiones de API o autenticación al contenedor de Laravel
    location ~ ^/(api|auth|login|logout|register|sanctum|_debugbar) {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass backend:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        # Ruta interna del contenedor backend donde está el index.php de Laravel
        fastcgi_param SCRIPT_FILENAME /var/www/html/backend/public/index.php;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
}
EOF

# 3. Reiniciar con limpieza de volúmenes para asegurar que no hay restos viejos
cd produccion
docker-compose down -v --remove-orphans
docker-compose up -d --build

# 4. Limpiar Laravel
echo "==> Optimizando Laravel..."
docker exec proximarkt-backend php artisan config:clear
docker exec proximarkt-backend php artisan route:clear