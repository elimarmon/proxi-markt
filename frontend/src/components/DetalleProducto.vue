<script setup>
import SolicitarCompra from './SolicitarCompra.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps(['id']);
const producto = ref(null);

const obtenerProducto = async () => {
    const response = await axios.get(`http://localhost:8080/api/productos/${props.id}`);
    producto.value = response.data;
}

onMounted(() => obtenerProducto());
</script>

<template>
    <h1>Probando...</h1>
    <div v-if="producto">
        <img :src="`http://localhost:8080/storage/${producto.imagen}`" :alt="producto.nombre_producto" />
        <h3>{{ producto.nombre_producto }}</h3>
    </div>
    
    <SolicitarCompra/>
</template>