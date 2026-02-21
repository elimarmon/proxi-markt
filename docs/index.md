# Proxi Markt

Esta aplicación pretende conectar a los agricultores, principalmente, con el mercado local. Ofrecer una plataforma de comercio justo libre de intermediarios.

## Características

* Permite compraventa directa sin interacción.
* Permite chatear entre usuarios para acordar detalles de compraventa.
* Sistema de confianza basado en valoraciones.
* Mantener un historial de compraventas.
* Funcionalidad para descubrir productores cercanos.

## Inicio rápido

Se trata de un monorepo con backend en Laravel y frontend en Vue. Para la persistencia de datos se usa MySQL y para la documentación se usa Mkdocs con Github Pages.

Los pasos a seguir para iniciar el proyecto son:

1. Clona el repositorio con `git clone https://github.com/eliseumarmon/proxi-markt.git`
2. En la carpeta raíz y ejecuta `docker compose up --build`
3. Entra en el contenedor de php con `docker compose exec -it php bash`
4. Una vez dentro del contenedor ejecuta los siguientes comandos:
   1. `composer install` -> instala todas las dependencias del proyecto
   2. `php artisan migrate` -> genera las tablas necesarias para el funcionamiento correcto de Laravel.
   3. `php artisan storage:link` -> enlaza la carpeta de imagenes del backend con el frontend.
   4. (Opcional) si quieres datos de prueba para testear el funcionamiento usar `php artisan db:seed`
5. Desde la raíz del proyecto, otra vez, ejecuta `cd frontend`
6. Instala dependencias de Vue con: `npm install`
7. Inicia el frontend con `npm run dev` (desarrollo).

[Detalles para iniciar el proyecto desde 0](documentacion_iniciar_proyecto.md)
