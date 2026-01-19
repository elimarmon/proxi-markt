<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import TarjetaProducto from "@/components/TarjetaProducto.vue";

const productos = ref([]);

const mostrarProductos = async () => {
    const response = await axios.get("http://localhost:8080/api/productos");
    productos.value = response.data;
    console.log(response);
};

onMounted(() => mostrarProductos());
</script>

<template>
    <h1>Hola desde productos</h1>
    <TarjetaProducto v-for="producto in productos" :id="producto.id" :id_categoria="producto.id_categoria" :nombre_producto="producto.nombre_producto" :descripcion="producto.descripcion" :precio="producto.precio" :stock_real="producto.stock_real"/>
</template>
