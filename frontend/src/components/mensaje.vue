<template>
  <navbar></navbar>
  <div class="contenedor-pagina">
    <div id="contenedor-titulo">
      <h1 class="titulo">Mensajes</h1>
      <p class="subtitulo">Conversaciones con compradores y vendedores</p>
    </div>

    <div id="contenedor-mensajes">
      <div class="contenido-mensajes">
        <div class="mensaje-card">
          <div class="mensaje-avatar">
            <img src="../assets/iconos/mi_cuenta_verde.png" alt="icono-cuenta">
          </div>

          <div class="mensaje-info">
            <div class="mensaje-header">
              <h3 class="nombre-usuario">Juan Martínez Ruiz</h3>
              <span class="fecha-mensaje">17 dic</span>
            </div>

            <div class="producto-tag">Tomates Ecológicos</div>

            <p class="texto-ultimo">Perfecto, nos vemos mañana a las 10.</p>
          </div>
        </div>

        <div class="mensaje-card">
          <div class="mensaje-avatar" style="background-color: #FF9800;">
             <img src="../assets/iconos/mi_cuenta_verde.png" alt="icono-cuenta">
          </div>
          <div class="mensaje-info">
            <div class="mensaje-header">
              <h3 class="nombre-usuario">Ana Gómez</h3>
              <span class="fecha-mensaje">16 dic</span>
            </div>
            <div class="producto-tag">
              Miel de Abeja
            </div>
            <p class="texto-ultimo">
              ¿Sigue disponible este producto?
            </p>
          </div>
        </div>

        <div class="mensaje-card">
          <div class="mensaje-avatar">
            <img src="../assets/iconos/mi_cuenta_verde.png" alt="icono-cuenta">
          </div>
          <div class="mensaje-info">
            <div class="mensaje-header">
              <h3 class="nombre-usuario">Carlos Ruiz</h3>
              <span class="fecha-mensaje">15 dic</span>
            </div>
            <div class="producto-tag">Aceite de Oliva</div>
            <p class="texto-ultimo">Gracias por la respuesta.</p>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import navbar from "./nav.vue";

const router = useRouter();

</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', 'Arial';
}

body {
  min-width: 400px;
}

.contenedor-pagina {
  margin-top: 80px;
  padding: 20px 50px;
}

#contenedor-titulo{
  max-width: 90%;
  margin: 40px auto 0 auto;
}

.titulo {
  font-family: sans-serif;
  color: #4CA626;
  margin-bottom: 10px;
  font-weight: bold;
}

.subtitulo {
  font-family: sans-serif;
  color: #666666;
  margin-bottom: 20px;
}

#contenedor-mensajes {
  max-width: 90%;
  margin-left: auto;
  margin-right: auto;
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}

.sin-mensajes {
  width: 100%;
  text-align: center;
  margin-top: 50px;
  color: #999999;
  font-size: 1.2rem;
  font-weight: bold;
  letter-spacing: 0.5px;
  padding: 30px;
  border: 1px solid #EEEEEE;
  border-radius: 12px;
  background-color: #FAFAFA;
}

.contenido-mensajes {
  width: 100%;
}

.mensaje-card {
  display: flex;
  padding: 15px 0;
  border-bottom: 1px solid #f0f0f0;
  gap: 15px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.mensaje-card:hover {
  background-color: #fcfcfc;
}

.mensaje-avatar {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-shrink: 0;
}

.mensaje-info {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.mensaje-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 4px;
}

.nombre-usuario {
  font-size: 1rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.fecha-mensaje {
  font-size: 0.8rem;
  color: #888;
}

.producto-tag {
  display: inline-block;
  align-self: flex-start;
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  padding: 2px 10px;
  font-size: 0.75rem;
  color: #444;
  margin-bottom: 6px;
  background-color: white;
}

.texto-ultimo {
  font-size: 0.95rem;
  color: #555;
  margin: 0;
  line-height: 1.4;
}
</style>