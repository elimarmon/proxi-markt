<script setup>
    import { useRouter } from 'vue-router';

    const router = useRouter();
defineProps({
    id: Number,
    id_categoria: Number,
    nombre_producto: String,
    descripcion: String,
    precio: Number,
    stock_real: Number,
    //imagen: String,
    //estado: Enumerator,
});


</script>

<template>
    <div>
        <div>
            <p>{{ id_categoria }}</p>
        </div>
        <div>
            <h4>{{ nombre_producto }}</h4>
            <p>{{ precio }}</p>
            <p>{{ descripcion }}</p>
            <p>{{ stock_real }}</p>
        </div>
    </div>
</template>
