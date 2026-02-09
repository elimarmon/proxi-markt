<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';

const compras = ref([]);


const miscompras = async () => {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://localhost:8080/api/miscompras', {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
    compras.value = response.data;
    console.log("compras: ", compras.value)
}

const formatearFecha = (fechaRaw) => {
    if (!fechaRaw) return 'Sin fecha';

    const fecha = new Date(fechaRaw);
    return new Intl.DateTimeFormat('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).format(fecha);
};

onMounted(miscompras)
</script>
<template>
    <div v-if="compras && compras.length > 0">
        <div v-for="compra in compras" :key="compra.id" class="compra-item">
            
            <div class="imagen-contenedor">
                <img :src="compra.producto.imagen ? `http://localhost:8080/storage/${compra.producto.imagen}` : 'https://via.placeholder.com/150'"
                    alt="Imagen producto">
            </div>

            <div class="info-contenedor">
                <h3>{{ compra.producto.nombre_producto }}</h3>
                
                <span class="badge-estado">{{ compra.producto.estado }}</span>
                
                <p class="detalle"><img src="../assets/iconos/stock.png" alt="" class="icono"> Cantidad: {{ compra.cantidad }}</p>
                <p class="detalle"><img src="../assets/iconos/calendario.png" alt="" class="icono"> Recogida: {{ formatearFecha(compra.fecha_prevista) }}</p>
                <p class="detalle"><img src="../assets/iconos/mi_cuenta_verde.png" alt="" class="icono"> Comprador: {{ compra.comprador.nombre_usuario }}</p>
            </div>

        </div>
    </div>
</template>
<style scoped>
.compra-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    margin-bottom: 10px;
    font-family: sans-serif;
}

.imagen-contenedor {
    position: relative;
    width: 80px;
    height: 80px;
}

.imagen-contenedor img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

.info-contenedor {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-contenedor h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #333;
}

.badge-estado {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    border: 1px solid #ccc;
    width: fit-content;
    margin-bottom: 5px;
}

.detalle {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: #666;
    margin: 0;
}

.icono {
    width: 25px;
    height: 25px;
}
</style>