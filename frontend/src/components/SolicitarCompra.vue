<script setup>
import axios from "axios";
import reactive from "axios";

const props = defineProps({
    id: {
        type: Number,
        required: true
    }
});

const form = reactive({
    cantidad: null,
    fecha: null,
    mensaje: '',
})

const solicitarCompra = async () => {

    const compraVenta = {
        cantidad: form.cantidad,
        fecha: form.fecha,
        mensaje: form.mensaje,
    }

    const data = await axios.post(`http://localhost:8080/api/compraventa/${props.id}`, compraVenta);

    if (data.status === 200) {
        alert("Solicitud creada correctamente.")
    }
    else {
        alert("Error en la solicitud.")
    }
};
</script>

<template>
    <form @prevent.default="solicitarCompra">
        <h3>Solicitar compra</h3>
        <label for="cantidad">Cantidad:</label>
        <br>
        <input v-model="cantidad" type="number" id="cantidad">
        <br><br>
        <label for="fecha">Fecha de recogida:</label>
        <br>
        <input v-model="fecha" type="date" id="fecha">
        <br><br>
        <label for="mensaje">Mensaje: (Opcional)</label>
        <br>
        <textarea v-model="mensaje" name="mensaje" id="mensaje">Escribe un mensaje...</textarea>
        <br><br>
        <button type="reset"">Limpiar</button> <button type=" submit">Enviar Solicitud</button>
    </form>
</template>

<style scoped></style>