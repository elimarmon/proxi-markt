#!/bin/bash
set -e

# 1. Sincronizar código desde la rama de despliegue
echo "==> [1/5] Actualizando código desde Git..."
git fetch origin
git reset --hard origin/despliegue

# 2. Configurar Nginx para SPA (Vue) y API (Laravel)
echo "==> [2/5] Configurando Nginx..."
cat << 'EOF' > docker/nginx/default.conf
server {
    listen 80;
    
    # Ruta donde Nginx busca el index.html de tu build de Vue
    root /var/www/html/frontend/dist; 
    index index.html;

    location / {
        # Si la ruta no es un archivo real, se la entrega a Vue (index.html)
        try_files $uri $uri/ /index.html;
    }

    # Redirigir peticiones de lógica al contenedor de PHP
    location ~ ^/(api|auth|login|logout|register|sanctum|_debugbar) {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass backend:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        
        # Ruta REAL dentro del contenedor proximarkt-backend comprobada con ls
        fastcgi_param SCRIPT_FILENAME /var/www/html/public/index.php;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
}
EOF

# 3. Reiniciar contenedores
cd produccion
echo "==> [3/5] Reiniciando contenedores de Docker..."
docker-compose down
docker-compose up -d --build

# 4. Control de Timing: Esperar a que MySQL esté listo
echo "==> [4/5] Esperando conexión estable con MySQL..."
MAX_RETRIES=30
COUNT=0

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
        echo "Error: MySQL no está disponible después de 60 segundos."
        exit 1
    fi
    echo "MySQL arrancando... (Intento $COUNT/$MAX_RETRIES)"
    sleep 2
done

echo "==> ¡MySQL conectado!"

# 5. Limpieza de caché y Migraciones
echo "==> [5/5] Ejecutando limpieza y migraciones de Laravel..."
docker exec proximarkt-backend php artisan optimize:clear
docker exec proximarkt-backend php artisan migrate --force

echo "----------------------------------------------------"
echo "==> ¡DESPLIEGUE FINALIZADO CON ÉXITO! <=="
echo "----------------------------------------------------"