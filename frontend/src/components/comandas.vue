<template>
    <navbar></navbar>
    <div class="contenedor-pagina">
        <div id="contenedor-titulo">
            <h1 class="titulo">Comandas</h1>
            <p class="subtitulo">Gestiona las solicitudes de compra de tus productos</p>
        </div>
        <div class="contenedor-comandas">
          <img src="../assets/iconos/stock.png" alt="Comandas pendientes" class="icono">
          <h3>Comandas pendientes</h3>
          <p>3 pendientes</p>

          <div class="comanda">
            <img src="../assets/fotos-prueba/tomate.webp" alt="foto-producto" class="foto-producto">
            <h3>Tomates ecológicos</h3>
            <p>Pendiente de Aprobación</p>
            <div id="precio-total">
              <p>17.50€</p>
              <p>Total</p>
            </div>
            
            <div id="cantidad">
              <img src="../assets/iconos/stock.png" alt="icono-cantidad" class="icono">
              <p>Cantidad: 5</p>
            </div>

            <div id="horario">
              <img src="../assets/iconos/calendario" alt="icono-calendario" class="icono">
              <p>19/1/2026</p>
            </div>

            <div id="usuario">
              <img src="../assets/iconos/cuenta.png" alt="icono-cuenta" class="icono">
              <p>Juan Carlos Martínez</p>
            </div>

            <div class="mensaje-comprador">
              <img src="../assets/iconos/chat-comanda.png" alt="icono-chat" class="icono">
              <p>Mensaje del comprador:</p>
              <p>...</p>
            </div>

            <button>
              <img src="../assets/iconos/aceptar.png" alt="icono-aceptar" class="icono">
              Aceptar comanda
            </button>

            <button>
              <img src="../assets/iconos/rechazar.png" alt="icono-rechazar" class="icono">
              Rechazar comanda
            </button>
          </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
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
  color: #4ca626;
  margin-bottom: 10px;
  font-weight: bold;
}

.subtitulo {
  font-family: sans-serif;
  color: #666666;
  margin-bottom: 20px;
}
</style>