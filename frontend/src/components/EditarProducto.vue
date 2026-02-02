<script setup>
import { reactive, onMounted, ref } from 'vue';
import axios from 'axios';
import navbar from './nav.vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const PuntosEntrega = ref([]);
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
    id_puntoentrega: "",
    imagen: null
});

const seleccionarArchivo = (e) => {
    const file = e.target.files[0];
    if (file) {
        archivoImagen.value = file;
        imagenPreview.value = URL.createObjectURL(file);
    }
}

const CargarProducto = async () => {
    try {
        const respuesta = await axios.get(`http://localhost:8080/api/productos/${props.id}`);
        Object.assign(formulario, respuesta.data);
        imagenPreview.value = null;
        archivoImagen.value = null;
    } catch (error) {
        console.error("Error cargando producto:", error);
    }
}

const editarProducto = async () => {
    const token = localStorage.getItem('token');

    const data = new FormData();
    // Laravel requiere _method PUT cuando se envía FormData via POST
    data.append('_method', 'PUT');
    data.append('nombre_producto', formulario.nombre_producto);
    data.append('descripcion', formulario.descripcion || '');
    data.append('precio', formulario.precio);
    data.append('stock_total', formulario.stock_total);
    data.append('id_puntoentrega', formulario.id_puntoentrega);

    if (archivoImagen.value) {
        data.append('imagen', archivoImagen.value);
    }

    try {
        const respuesta = await axios.post(`http://localhost:8080/api/productos/${props.id}`, data, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'multipart/form-data'
            }
        });

        if (respuesta.status === 200 || respuesta.status === 204) {
            router.push('/cuenta');
        }
    } catch (error) {
        console.error("Error al editar:", error);
    }
}

const CargarPuntos = async () => {
    const token = localStorage.getItem('token');
    try {
        const resposta = await axios.get(`http://localhost:8080/api/puntosuser`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        PuntosEntrega.value = resposta.data;
    } catch (error) {
        console.error("Error cargando puntos:", error);
    }
}

onMounted(() => {
    CargarProducto();
    CargarPuntos();
});
</script>

<template>
    <navbar></navbar>
    <div class="contenedor-edicion">
        <div class="tarjeta-formulario">
            <h1 class="titulo-principal">Edición de producto</h1>

            <form @submit.prevent="editarProducto">
                <div class="grupo-campo">
                    <label for="nombre">Nombre producto</label>
                    <input v-model="formulario.nombre_producto" type="text" id="nombre"
                        placeholder="Ej: Manzanas Orgánicas">
                </div>

                <div class="grupo-campo">
                    <label for="descripcion">Descripción del producto</label>
                    <input v-model="formulario.descripcion" type="text" id="descripcion"
                        placeholder="Breve descripción...">
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

                <div class="dos-columnas">
                    <div class="grupo-campo">
                        <label for="precio">Precio (€)</label>
                        <input v-model="formulario.precio" type="number" step="0.01" id="precio">
                    </div>

                    <div class="grupo-campo">
                        <label for="stock">Stock disponible</label>
                        <input v-model="formulario.stock_total" type="number" id="stock">
                    </div>
                </div>

                <div class="grupo-campo">
                    <label>Imagen del producto</label>

                    <div class="preview-container" v-if="imagenPreview || formulario.imagen">
                        <img :src="imagenPreview || `http://localhost:8080/storage/${formulario.imagen}`" alt="Vista previa"
                            class="foto-preview" />
                        <p class="texto-ayuda-foto">{{ imagenPreview ? 'Nueva imagen seleccionada' : 'Imagen actual' }}
                        </p>
                    </div>

                    <div class="zona-upload">
                        <input type="file" @change="seleccionarArchivo" id="imagen" accept="image/*"
                            class="input-file-oculto">
                        <div class="diseno-upload">
                            <span class="icono-nube">↑</span>
                            <p>Haz clic para cambiar la imagen</p>
                            <small>PNG, JPG o WEBP (máx. 5MB)</small>
                        </div>
                    </div>
                </div>

                <button type="submit" class="boton-actualizar">Actualizar Producto</button>
            </form>
        </div>
    </div>
</template>

<style scoped>
input,
select,
textarea,
button {
    font-family: inherit;
}

.contenedor-edicion {
    display: flex;
    justify-content: center;
    padding: 80px 20px 40px;
    background-color: #f9f9f9;
    min-height: 100vh;
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    box-sizing: border-box;
}

.tarjeta-formulario {
    background: #ffffff;
    width: 100%;
    max-width: 650px;
    padding: 32px;
    border-radius: 12px;
    border: 1px solid #edf2f7;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    height: fit-content;
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

.dos-columnas {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #4A5568;
    margin-bottom: 8px;
}

input,
select {
    width: 100%;
    padding: 12px 14px;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
    box-sizing: border-box;
    color: #2d3748;
}

input:focus,
select:focus {
    outline: none;
    border-color: #4CA626;
    background-color: #ffffff;
    box-shadow: 0 0 0 3px rgba(76, 166, 38, 0.1);
}

.ayuda-texto {
    font-size: 12px;
    color: #718096;
    margin-top: 6px;
}

.texto-ayuda-foto {
    font-size: 12px;
    color: #4CA626;
    font-weight: 500;
    margin-top: 8px;
}

.zona-upload {
    position: relative;
    border: 2px dashed #E2E8f0;
    border-radius: 10px;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background-color: #fff;
    transition: all 0.2s;
    min-height: 120px;
}

.zona-upload:hover {
    background-color: #f9fafb;
    border-color: #4CA626;
}

.input-file-oculto {
    position: absolute;
    top: 0;
    left: 0;
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
    margin-bottom: 4px;
}

.preview-container {
    margin-bottom: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.foto-preview {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 12px;
    border: 3px solid #f0f4f8;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.boton-actualizar {
    width: 100%;
    padding: 14px;
    margin-top: 10px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    border: none;
    color: white;
    background: linear-gradient(90deg, #4CA626 0%, #009B58 100%);
    transition: transform 0.1s active, opacity 0.2s;
}

.boton-actualizar:hover {
    opacity: 0.95;
    box-shadow: 0 4px 12px rgba(76, 166, 38, 0.2);
}

.boton-actualizar:active {
    transform: scale(0.98);
}

@media (max-width: 480px) {
    .contenedor-edicion {
        padding-top: 70px;
    }

    .tarjeta-formulario {
        padding: 20px;
    }

    .dos-columnas {
        grid-template-columns: 1fr;
        gap: 0;
    }
}
</style>