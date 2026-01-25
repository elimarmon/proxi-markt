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

1. En la carpeta raíz `docker compose up --build`
2. Entrar en el contenedor de php con `docker compose exec -it php bash`
3. Una vez dentro del contenedor ejecutar los siguientes comandos:
   1. `php artisan migrate` -> genera las tablas necesarias para el funcionamiento correcto de Laravel.
   2. `php artisan storage:link` -> enlaza la carpeta de imagenes del backend con el frontend.
4. Desde la raíz del proyecto, otra vez, ejecutamos `cd frontend`
5. Se inicia el frontend con `npm run dev` (desarrollo).

## Información adicional

En progreso...
