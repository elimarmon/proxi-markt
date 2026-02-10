# Etapa 1: Construcción (Build Stage)
FROM node:22-alpine AS build-stage

WORKDIR /app

# Copiamos archivos de dependencias
COPY package*.json ./
RUN npm install

# Copiamos el resto del código
COPY . .

# Argumentos de construcción para Vite
ARG VITE_API_URL
ENV VITE_API_URL=$VITE_API_URL

# Generamos los archivos estáticos (carpeta dist)
RUN npm run build

# Etapa 2: Servidor Web (Production Stage)
FROM nginx:stable-alpine

# 1. Limpiamos la configuración por defecto de Nginx para evitar conflictos
RUN rm /etc/nginx/conf.d/default.conf

# 2. Copiamos los archivos estáticos generados en la etapa anterior
COPY --from=build-stage /app/dist /usr/share/nginx/html

# 3. Copiamos TU archivo de configuración personalizado
COPY default.conf /etc/nginx/conf.d/default.conf

# Exponemos el puerto 80
EXPOSE 80

# Arrancamos Nginx
CMD ["nginx", "-g", "daemon off;"]