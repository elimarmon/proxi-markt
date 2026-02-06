<script setup>
import axios from 'axios';
import { reactive, onMounted } from 'vue';


</script>
<template>
    <form @submit.prevent="enviarValoracion">
        <label for="valoracion">Puntuación:</label><br>
        <input v-model="form.estrellas" id="valoracion" name="valoracion" type="number" step="0.5">
        <br><br>
        <label for="comentario">Mensaje:</label><br>
        <textarea v-model="form.comentario" name="comentario" id="comentario"></textarea>
        <button type="submit">Valorar</button>
    </form>
</template>