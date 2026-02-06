<script setup>
import { reactive } from 'vue';

const emit = defineEmits(['enviar-valoracion']);

const form = reactive({
    valoracion: 0,
    comentario: ''
});

const enviarValoracion = () => {
    emit('enviar-valoracion', {
        valoracion: form.valoracion,
        comentario: form.comentario
    });
}

</script>
<template>
    <div>
        <form @submit.prevent="enviarValoracion">
            <label for="valoracion">Puntuación:</label><br>
            <input v-model="form.valoracion" id="valoracion" name="valoracion" type="number" step="0.5">
            <br><br>
            <label for="comentario">Mensaje:</label><br>
            <textarea v-model="form.comentario" name="comentario" id="comentario"></textarea>
            <button type="submit">Valorar</button>
        </form>
    </div>
</template>