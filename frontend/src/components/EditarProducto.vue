<script setup>
import { reactive, onMounted, ref } from 'vue';
import axios from 'axios';
import navbar from './nav.vue'
import { useRouter } from 'vue-router';

const router = useRouter()

const PuntosEntrega = ref([])
const archivoImagen = ref(null);
const imagenPreview = ref(null);


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
    id_puntoentrega: ""
})

const seleccionarArchivo = (e) => {
    const file = e.target.files[0];
    if (file) {
        archivoImagen.value = file;
        imagenPreview.value = URL.createObjectURL(file);
    }
}

const CargarProducto = async () => {
    const producto = await axios.get('http://localhost:8080/api/productos/' + props.id)
    const datosproducto = producto.data;

    Object.assign(formulario, datosproducto)

    imagenPreview.value = null;
    archivoImagen.value = null;

}

const emits = defineEmits(['editar']);

const editarProducto = async () => {
    const token = localStorage.getItem('token');

    const data = new FormData();
    data.append('_method', 'PUT'); // per a que laravel eu trate com un PUT
    data.append('nombre_producto', formulario.nombre_producto);
    data.append('descripcion', formulario.descripcion || '');
    data.append('precio', formulario.precio);
    data.append('stock_total', formulario.stock_total);
    data.append('id_puntoentrega', formulario.id_puntoentrega);

    if (archivoImagen.value) {
        data.append('imagen', archivoImagen.value);
    }

    // fem un post per a que laravel trate be en el archiu
    const editar = await axios.post('http://localhost:8080/api/productos/' + props.id, data, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'multipart/form-data'
        }
    })

    if (editar.status === 200) {
        router.push('/cuenta')
    }
}

const CargarPuntos = async () => {
    const token = localStorage.getItem('token');
    const resposta = await axios.get('http://localhost:8080/api/puntosuser', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    })
    PuntosEntrega.value = resposta.data;
}

onMounted(() => {
    CargarProducto();
    CargarPuntos();
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
                    <input v-model="formulario.nombre_producto" type="text" id="nombre">
                </div>

                <div class="grupo-campo">
                    <label for="descripcion">Descripcion del producto</label>
                    <input v-model="formulario.descripcion" type="text" id="descripcion">
                </div>

                <div class="grupo-campo">
                    <label for="punto">Punto de entrega</label>
                    <select v-model="formulario.id_puntoentrega" id="punto">
                        <option value="" disabled>Selecciona un punto</option>
                        <option v-for="punto in PuntosEntrega" :key="punto.id" :value="punto.id">
                            {{ punto.nombre_punto }}
                        </option>
                    </select>
                    <p class="ayuda-texto">Indica dónde el comprador podrá recoger el producto</p>
                </div>

                <div class="grupo-campo">
                    <label for="precio">Precio del producto</label>
                    <input v-model="formulario.precio" type="decimal" id="precio">
                </div>

                <div class="grupo-campo">
                    <label for="stock">Stock del producto</label>
                    <input v-model="formulario.stock_total" type="number" id="stock">
                </div>

                <div class="grupo-campo">
                    <label for="imagen">Imagen del producto (opcional)</label>

                    <div class="preview-container" v-if="imagenPreview || formulario.imagen">
                        <img :src="imagenPreview || `http://localhost:8080/storage/${formulario.imagen}`"
                            alt="Vista previa" class="foto-preview" />
                        <p class="texto-ayuda">{{ imagenPreview ? 'Nueva imagen seleccionada' : 'Imagen actual' }}</p>
                    </div>

                    <div class="zona-upload">
                        <input type="file" @change="seleccionarArchivo" id="imagen" accept="image/*" class="input-file-oculto">
                        <div class="diseno-upload">
                            <span class="icono-nube">↑</span>
                            <p>Haz clic para subir o arrastra una imagen</p>
                            <small>PNG, JPG o WEBP (máx. 5MB)</small>
                        </div>
                    </div>
                </div>

                <button type="submit" class="boton-actualizar">Actualizar</button>
            </form>
        </div>
    </div>
</template>

<style scoped>
.contenedor-edicion {
    display: flex;
    justify-content: center;
    padding: 60px 20px;
    background-color: #f9f9f9;
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.tarjeta-formulario {
    background: #ffffff;
    width: 100%;
    max-width: 650px; /* Un poco más ancha como en la imagen */
    padding: 40px;
    border-radius: 12px;
    border: 1px solid #edf2f7;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.titulo-principal {
    color: #4CA626;
    font-size: 24px;
    margin-bottom: 30px;
    font-weight: bold;
    text-align: center;
}

.grupo-campo {
    margin-bottom: 20px;
}

label {
    display: block;
    font-size: 13.5px;
    font-weight: 500;
    color: #4A5568;
    margin-bottom: 8px;
}

input, select {
    width: 100%;
    padding: 11px 14px;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

input:focus, select:focus {
    outline: none;
    border-color: #4CA626;
    background-color: #ffffff;
    box-shadow: 0 0 0 3px rgba(76, 166, 38, 0.1);
}

.ayuda-texto {
    font-size: 12px;
    color: #A0AEC0;
    margin-top: 4px;
}

/* Estilos para la zona de carga de imagen */
.zona-upload {
    position: relative;
    border: 2px dashed #E2E8f0;
    border-radius: 10px;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background-color: #fff;
    transition: all 0.2s;
}

.zona-upload:hover {
    background-color: #f9fafb;
    border-color: #4CA626;
}

.input-file-oculto {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}

.diseno-upload {
    text-align: center;
    color: #718096;
}

.icono-nube {
    font-size: 24px;
    display: block;
    margin-bottom: 8px;
}

.diseno-upload p {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 4px;
}

.diseno-upload small {
    font-size: 11px;
    color: #A0AEC0;
}

.preview-container {
    margin-bottom: 15px;
    text-align: center;
}

.foto-preview {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #edf2f7;
}

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
}
</style>