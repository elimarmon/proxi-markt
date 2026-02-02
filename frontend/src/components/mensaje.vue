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
            Apartado mensajes
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import navbar from "./nav.vue";

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
    color: #4ca626;
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
</style>