<script setup>
import { reactive, onMounted } from 'vue';
import axios from 'axios';
import navbar from './nav.vue'
import { useRouter } from 'vue-router';

const router = useRouter()


const props = defineProps({
    id: {
        type: [String, Number],
        required: true
    }
});

const formulario = reactive({
    id: props.id,
    nombre_producto: "",
    descripcion: "",
    precio: 0,
    stock_total: 0,
})

const CargarProducto = async () => {
    const producto = await axios.get('http://localhost:8080/api/productos/' + props.id)
    const datosproducto = producto.data;

    Object.assign(formulario, datosproducto)

}

const emits = defineEmits(['editar']);

const editarProducto = async () => {
    const token = localStorage.getItem('token');

    const editar = await axios.put('http://localhost:8080/api/productos/' + props.id, formulario, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    })

    if (editar.status === 200) {
        router.push('/cuenta')
    }

}

onMounted(() => {
    CargarProducto();
})

</script>
<template>
    <navbar></navbar>
    <div class="contenedor-edicion">
        <div class="tarjeta-formulario">
            <h1 class="titulo-principal">Edición de producto</h1>

            <form @submit.prevent="editarProducto">
                <div class="grupo-campo">
                    <label for="nombre">Nombre producto</label>
                    <input v-model="formulario.nombre_producto" type="text" name="nombre" id="nombre">
                </div>

                <div class="grupo-campo">
                    <label for="descripcion">Descripcion del producto</label>
                    <input v-model="formulario.descripcion" type="text" name="descripcion" id="descripcion">
                </div>

                <div class="grupo-campo">
                    <label for="precio">Precio del producto</label>
                    <input v-model="formulario.precio" type="number" name="precio" id="precio">
                </div>

                <div class="grupo-campo">
                    <label for="stock">Stock del producto</label>
                    <input v-model="formulario.stock_total" type="number" name="stock" id="stock">
                </div>

                <button type="submit" class="boton-actualizar">Actualizar</button>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Contenedor para centrar y dar aire a la página */
.contenedor-edicion {
    display: flex;
    justify-content: center;
    padding: 60px 20px;
    background-color: #f9f9f9;
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Tarjeta con la estética del ejemplo */
.tarjeta-formulario {
    background: #ffffff;
    width: 100%;
    max-width: 550px;
    padding: 40px;
    border-radius: 12px;
    border: 1px solid #edf2f7;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
}

/* Estilo del título */
.titulo-principal {
    color: #4CA626;
    font-size: 24px;
    margin-bottom: 30px;
    font-weight: bold;
    text-align: center;
}

/* Espaciado de los campos */
.grupo-campo {
    margin-bottom: 20px;
}

label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #4A5568;
    margin-bottom: 8px;
}

/* Estética de los inputs */
input {
    width: 100%;
    padding: 12px 15px;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    box-sizing: border-box;
    /* Evita que el padding rompa el ancho */
}

input:focus {
    outline: none;
    border-color: #4CA626;
    background-color: #ffffff;
    box-shadow: 0 0 0 3px rgba(76, 166, 38, 0.1);
}

/* Botón con el degradado verde */
.boton-actualizar {
    width: 100%;
    padding: 14px;
    margin-top: 10px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    border: none;
    color: white;
    background: linear-gradient(90deg, #4CA626 0%, #009B58 100%);
    transition: transform 0.2s, filter 0.2s;
}

.boton-actualizar:hover {
    filter: brightness(1.05);
    transform: translateY(-1px);
}

.boton-actualizar:active {
    transform: translateY(0);
}
</style>