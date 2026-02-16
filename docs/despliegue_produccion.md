# Despliegue en producción

Esta guía está pensada para seguir el despliegue desde 0.
Se explica qué hace cada componente y para qué sirve cada comando.

## 1. Arquitectura: qué hace cada pieza

### Traefik (entrada pública)

Traefik es el proxy que recibe el tráfico desde Internet.
Su función es:

- Escuchar peticiones en `80` y `443`.
- Gestionar certificados HTTPS de Let's Encrypt.
- Redirigir HTTP a HTTPS.
- Enviar las peticiones al contenedor correcto (`nginx`) según el dominio.

En este proyecto, Traefik es quien publica la web al exterior.

### Nginx (servidor web de la aplicación)

Nginx está detrás de Traefik, dentro de la red interna de Docker.
Su función es:

- Servir el frontend (Vue) como archivos estáticos.
- Recibir rutas `/api/*` y pasarlas a Laravel (`php-fpm`).

Traefik decide "a qué servicio enviar"; Nginx decide "cómo responder cada ruta".

### Contenedor `php` (Laravel)

Este contenedor ejecuta el backend Laravel con `php-fpm`.
Aquí se procesan:

- Lógica de negocio.
- Autenticación.
- Endpoints `/api/*`.
- Comandos `php artisan`.

### Contenedor `mysql` (base de datos)

Aquí se guardan los datos de la aplicación.
La estructura inicial de tablas está en `docker/database/base.sql`.

## 2. Requisitos previos

Antes de desplegar:

- Docker y Docker Compose instalados en el servidor.
- El dominio apuntando a la IP del servidor.
- Puertos `80` y `443` accesibles.
- Repositorio clonado en el servidor.

## 3. Parte 1: desplegar desde 0

### 3.1 Clonar repositorio y entrar en la carpeta

```bash
# Clona el repositorio en la carpeta de proyectos del servidor
git clone <URL_DEL_REPO> ~/proyectos/proxi-markt
# Entra en la carpeta raíz del proyecto
cd ~/proyectos/proxi-markt
```

Explicación:

- `git clone`: descarga el código en el servidor.
- `cd`: entra en la carpeta para ejecutar el resto de comandos.

### 3.2 Levantar contenedores

```bash
# Levanta todos los servicios en segundo plano y reconstruye imágenes
docker compose -f despliegue/dockercompose.prod.yml up -d --build
```

Explicación:

- `-f ...prod.yml`: usa el compose de producción.
- `--build`: reconstruye imágenes (backend/frontend embebido en imagen de `php`).
- `-d`: deja los contenedores en segundo plano.

### 3.3 Cargar estructura inicial de base de datos (tablas)

```bash
# Importa la estructura inicial de la base de datos (tablas y relaciones)
docker compose -f despliegue/dockercompose.prod.yml exec -T mysql \
  mysql -ualumno -palumno -D proxi_markt < docker/database/base.sql
```

Explicación:

- `exec -T mysql`: ejecuta comando dentro del contenedor de MySQL.
- `mysql ... -D proxi_markt`: selecciona la base de datos de la app.
- `< base.sql`: importa tablas y estructura inicial.

### 3.4 Ejecutar comandos de Laravel

```bash
# Aplica migraciones pendientes de Laravel en producción
docker compose -f despliegue/dockercompose.prod.yml exec php sh -lc "cd /var/www/html/backend && php artisan migrate --force"
# Crea el enlace simbólico de storage para servir imágenes
docker compose -f despliegue/dockercompose.prod.yml exec php sh -lc "cd /var/www/html/backend && php artisan storage:link"
# Limpia la caché de Laravel (configuración, rutas, vistas, etc.)
docker compose -f despliegue/dockercompose.prod.yml exec php sh -lc "cd /var/www/html/backend && php artisan optimize:clear"
```

Explicación:

- `migrate --force`: aplica migraciones en entorno producción sin pedir confirmación.
- `storage:link`: crea enlace simbólico para exponer imágenes del backend.
- `optimize:clear`: limpia cachés (config, rutas, vistas) para evitar configuraciones antiguas.

### 3.5 Generar frontend para producción

```bash
# Entra en la carpeta del frontend
cd frontend
# Instala dependencias de Node.js del frontend
npm install
# Genera la versión compilada para producción en frontend/dist
npm run build
# Vuelve a la raíz del proyecto
cd ..
# Reinicia Nginx para que sirva la build recién generada
docker compose -f despliegue/dockercompose.prod.yml restart nginx
```

Explicación:

- `npm install`: instala dependencias del frontend.
- `npm run build`: genera `frontend/dist` para producción.
- `restart nginx`: recarga Nginx para servir la última build.

### 3.6 Comprobar que todo funciona

```bash
# Comprueba que la web pública (frontend) responde
curl -i https://proximarkt.francecentral.cloudapp.azure.com/
# Comprueba que la API del backend responde correctamente
curl -i https://proximarkt.francecentral.cloudapp.azure.com/api/productos
```

Explicación:

- Primera URL: valida que el frontend responde.
- Segunda URL: valida que la API de Laravel responde.

Si ambas responden (`200`, `301` o JSON válido), despliegue correcto.

## 4. Parte 2: actualizar a la última versión del repositorio

Este bloque se usa cada vez que se suben cambios.

```bash
# Entra en la carpeta del proyecto desplegado
cd ~/proyectos/proxi-markt
# Descarga los últimos cambios del repositorio remoto
git pull
# Reconstruye y actualiza los contenedores con los cambios nuevos
docker compose -f despliegue/dockercompose.prod.yml up -d --build
# Ejecuta migraciones y limpia caché de Laravel tras actualizar código
docker compose -f despliegue/dockercompose.prod.yml exec php sh -lc "cd /var/www/html/backend && php artisan migrate --force && php artisan optimize:clear"
# Entra en frontend para actualizar dependencias/build si hay cambios web
cd frontend
# Instala/actualiza dependencias de frontend
npm install
# Genera la nueva build del frontend
npm run build
# Vuelve a la carpeta raíz del proyecto
cd ..
# Reinicia Nginx para publicar la nueva build
docker compose -f despliegue/dockercompose.prod.yml restart nginx
```

Explicación:

- `git pull`: trae el último código del repositorio.
- `up -d --build`: recompone contenedores con los cambios.
- `migrate --force`: actualiza la BD si hay migraciones nuevas.
- `optimize:clear`: evita usar caché antigua de Laravel.
- `npm run build`: publica los cambios del frontend.
- `restart nginx`: hace visible la nueva build.

### 4.1 Comprobar que la actualización funciona

```bash
# Comprueba que la web pública (frontend) responde
curl -i https://proximarkt.francecentral.cloudapp.azure.com/
# Comprueba que la API del backend responde correctamente
curl -i https://proximarkt.francecentral.cloudapp.azure.com/api/productos
```

Explicación:

- Primera URL: valida que el frontend responde tras la actualización.
- Segunda URL: valida que la API sigue respondiendo tras la actualización.

## 5. Diagnóstico rápido (si hay errores)

```bash
# Muestra el estado actual de los contenedores del despliegue
docker compose -f despliegue/dockercompose.prod.yml ps
# Muestra los últimos logs de Nginx, PHP y Traefik para detectar errores
docker compose -f despliegue/dockercompose.prod.yml logs --tail=200 nginx php traefik
# Valida la sintaxis de la configuración de Nginx
docker compose -f despliegue/dockercompose.prod.yml exec nginx nginx -t
```

Explicación:

- `ps`: comprueba que los contenedores estén levantados.
- `logs`: muestra errores recientes de Nginx, PHP y Traefik.
- `nginx -t`: valida sintaxis de configuración de Nginx.

## 6. Errores frecuentes y solución

- `/api/*` devuelve `500`:
  - Suele ser un error interno de Laravel (BD, `.env`, validaciones o código).
  - Solución paso a paso:

```bash
# Muestra el error real de Laravel en el log
docker compose -f despliegue/dockercompose.prod.yml exec php sh -lc "tail -n 120 /var/www/html/backend/storage/logs/laravel.log"
# Limpia caché de Laravel por si hay configuración antigua
docker compose -f despliegue/dockercompose.prod.yml exec php sh -lc "cd /var/www/html/backend && php artisan optimize:clear"
# Verifica que las variables DB están cargadas en el contenedor php
docker compose -f despliegue/dockercompose.prod.yml exec php env | grep '^DB_'
```

- `/` devuelve `403`:
  - Normalmente falta la build del frontend (`frontend/dist`) o no tiene permisos.
  - Solución paso a paso:

```bash
# Entra en la carpeta frontend
cd ~/proyectos/proxi-markt/frontend
# Instala dependencias del frontend
npm install
# Genera de nuevo la build de producción
npm run build
# Vuelve a la raíz del proyecto
cd ..
# Da permisos de lectura/ejecución al contenido de dist
chmod -R a+rX frontend/dist
# Reinicia Nginx para que cargue la build actualizada
docker compose -f despliegue/dockercompose.prod.yml restart nginx
```

- `Could not open input file: artisan`:
  - Estás ejecutando `php artisan` fuera de la carpeta de Laravel.
  - Solución paso a paso:

```bash
# Entra al contenedor de php
docker compose -f despliegue/dockercompose.prod.yml exec php sh
# Entra en la carpeta correcta del backend Laravel
cd /var/www/html/backend
# Comprueba que existe el archivo artisan
ls -la artisan
# Ejecuta el comando artisan que necesites
php artisan optimize:clear
```

- La base de datos existe pero no hay tablas:
  - Ocurre cuando MySQL crea `proxi_markt` pero no se importa `base.sql`.
  - Solución paso a paso:

```bash
# Importa la estructura inicial de tablas en la base proxi_markt
docker compose -f despliegue/dockercompose.prod.yml exec -T mysql \
  mysql -ualumno -palumno -D proxi_markt < docker/database/base.sql
# Comprueba que ya existen tablas dentro de la base de datos
docker compose -f despliegue/dockercompose.prod.yml exec mysql \
  mysql -ualumno -palumno -D proxi_markt -e "SHOW TABLES;"
```
