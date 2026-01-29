<script setup>
import { reactive } from "vue";

const emit = defineEmits(['enviar-solicitud']);

const form = reactive({
    cantidad: null,
    fecha: null,
    mensaje: '',
})

const enviarSolicitud = async () => {

    emit('enviar-solicitud', {
        cantidad: form.cantidad,
        fecha_prevista: form.fecha,
        mensaje: form.mensaje,
    });

    
};
</script>

<template>
    <form @submit.prevent="enviarSolicitud">
        <h3>Solicitar compra</h3>
        <label for="cantidad">Cantidad:</label>
        <br>
        <input v-model="form.cantidad" type="number" id="cantidad">
        <br><br>
        <label for="fecha">Fecha de recogida:</label>
        <br>
        <input v-model="form.fecha" type="date" id="fecha">
        <br><br>
        <label for="mensaje">Mensaje: (Opcional)</label>
        <br>
        <textarea v-model="form.mensaje" name="mensaje" id="mensaje">Escribe un mensaje...</textarea>
        <br><br>
        <button type="reset"">Limpiar</button> <button type=" submit">Enviar Solicitud</button>
    </form>
</template>

<style scoped></style>