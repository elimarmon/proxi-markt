<template>
    <navbar></navbar>
    <div class="contenedor-pagina">
        <div id="contenedor-titulo">
            <h1 class="titulo">Mensajes</h1>
            <p class="subtitulo">
                Conversaciones con compradores y vendedores
            </p>
        </div>

    <div id="contenedor-mensajes">
      <div v-if="mensajes.length == 0" class="sin-mensajes">
        Aún no tienes mensajes
      </div>
      
      <div v-else>
        <div class="mensaje">
          <img src="../assets/iconos/cuenta.png" alt="icono-cuenta">
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
const mensajes = ref([]);
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

#contenedor-titulo {
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
</style>