FROM node:18-alpine AS build-stage
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
# Asegúrate de que VITE_API_URL esté presente en el build
ARG VITE_API_URL
ENV VITE_API_URL=$VITE_API_URL
RUN npm run build

FROM nginx:stable-alpine
# Copiamos el build al directorio de Nginx
COPY --from=build-stage /app/dist /usr/share/nginx/html
# IMPORTANTE: Configuración de Nginx para SPA (evita el 404 al recargar)
COPY default.conf /etc/nginx/conf.d/default.conf
EXPOSE 80