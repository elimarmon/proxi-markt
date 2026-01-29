<script setup>
import SolicitarCompra from './SolicitarCompra.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Navbar from "@/components/nav.vue";

const props = defineProps(['id']);
const producto = ref(null);

const obtenerProducto = async () => {
    const response = await axios.get(`http://localhost:8080/api/productos/${props.id}`);
    producto.value = response.data;
}

const token = localStorage.getItem('token');

const crearCompraventa = (datosCompra) => {
    const payload = {
        id_producto: producto.value.id,
        id_vendedor: producto.value.id_usuario,
        id_punto: producto.value.id_puntoentrega,
        cantidad: datosCompra.cantidad,
        precio: producto.value.precio,
        fecha_prevista: datosCompra.fecha,
    }
    axios.post(`http://localhost:8080/api/compraventa/${props.id}`, payload, { headers: { 'Authorization': `Bearer ${token}` } })
}

onMounted(() => obtenerProducto());
</script>

<template>
    <Navbar />
    <h1>Detalle producto.</h1>
    <div v-if="producto">
        <img :src="`http://localhost:8080/storage/${producto.imagen}`" :alt="producto.nombre_producto" />
        <h3>{{ producto.nombre_producto }}</h3>
    </div>

    <SolicitarCompra @enviar-solicitud="crearCompraventa" />
</template>