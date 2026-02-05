<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Navbar from './nav.vue'
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';

const router = useRouter()

const puntosEntrega = ref([])
const categorias = ref([])
const nombreProducto = ref('');
const descripcion = ref('');
const precio = ref(0);
const stock = ref(0);
const puntoEntrega = ref('');
const categoria = ref('');
const imagen = ref(null);
const cargando = ref(false);
const { usuario, fetchUsuario } = useAuth();

const formularioIncompleto = computed(() => {
    return nombreProducto.value.trim() === '' ||
        descripcion.value.trim() === '' ||
        precio.value <= 0 ||
        stock.value <= 0 ||
        categoria.value === '' ||
        puntoEntrega.value === '' ||
        puntosEntrega.value.length === 0;
});

const guardarImagen = (event) => {
    imagen.value = event.target.files[0];
}

const insertarProducto = async () => {
    const token = localStorage.getItem('token');
    try {
        const datos = new FormData();
        datos.append('nombre_producto', nombreProducto.value);
        datos.append('descripcion', descripcion.value);
        datos.append('precio', precio.value);
        datos.append('stock_total', stock.value);
        datos.append('id_categoria', categoria.value);
        datos.append('id_puntoentrega', puntoEntrega.value);

        if (imagen.value) {
            datos.append('imagen', imagen.value);
        }

        const respuesta = await axios.post('http://localhost:8080/api/productos', datos, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'multipart/form-data',
                'Accept': 'application/json'
            }
        });

        if (respuesta.status === 201 || respuesta.status === 200) {
            alert('¡Producto creado correctamente!');
            router.push('/cuenta');
        }
    } catch (error) {
        console.error("Error:", error.response?.data);
        alert('Error: ' + (error.response?.data?.message || 'No se pudo crear'));
    }
}

const cargarPuntos = async () => {

    cargando.value = true;

    const token = localStorage.getItem('token');

    try {
        const resposta = await axios.get(`http://localhost:8080/api/usuarios/${usuario.value.id}/puntos`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'applicacion/json'
            }
        });
        puntosEntrega.value = resposta.data;
    } catch (err) {
        alert("Error cargando puntos de entrega.");
        console.error(err.message);
    } finally {
        cargando.value = false;
    }
}

const cargarCategorias = async () => {
    const resposta = await axios.get('http://localhost:8080/api/categorias');
    categorias.value = resposta.data;
}

onMounted(async () => {
    await fetchUsuario();
    cargarPuntos();
    cargarCategorias();
});
</script>

<template>
    <Navbar></Navbar>
    <div class="contenedor-pagina">
        <div id="contenedor-titulo">
            <h1 class="titulo">Publicar Producto</h1>
            <p class="subtitulo">Comparte tus frutas y verduras frescas con la comunidad</p>
        </div>

        <div class="tarjeta-formulario">
            <h3 class="header-interno">Publicar nuevo producto</h3>

            <form @submit.prevent="insertarProducto">
                <div class="campo">
                    <label>Nombre del producto</label>
                    <input v-model="nombreProducto" type="text" placeholder="Ej: Tomates orgánicos" required>
                </div>

                <div class="campo">
                    <label>Descripción</label>
                    <textarea v-model="descripcion" placeholder="Describe tu producto en detalle..."
                        required></textarea>
                </div>

                <div class="fila-doble">
                    <div class="columna">
                        <label>Precio (€)</label>
                        <input v-model="precio" type="number" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="columna">
                        <label>Stock disponible</label>
                        <input v-model="stock" type="number" placeholder="Cantidad" required>
                    </div>
                </div>

                <div class="campo">
                    <label>Categoría</label>
                    <select v-model="categoria" required>
                        <option value="" disabled>Selecciona una categoría</option>
                        <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                            {{ cat.nombre_categoria }}
                        </option>
                    </select>
                </div>

                <div class="campo">
                    <label>Punto de entrega</label>
                    <p class="ayuda-texto" v-if="cargando">Cargando puntos...</p>
                    <p class="ayuda-texto" v-else-if="!cargando && puntosEntrega.length > 0">Indica dónde el comprador
                        podrá recoger el producto</p>

                    <select v-if="puntosEntrega.length > 0" v-model="puntoEntrega" required>
                        <option value="" disabled>Ej: Mercado de Santa Caterina</option>
                        <option v-for="punto in puntosEntrega" :key="punto.id" :value="punto.id">
                            {{ punto.nombre_punto }}
                        </option>
                    </select>

                    <p v-else-if="!cargando && puntosEntrega.lengt == 0" class="error-texto">* Debes crear al menos un
                        punto de entrega en tu perfil.</p>
                </div>

                <div class="campo">
                    <label>Imagen del producto (opcional)</label>
                    <div class="zona-upload">
                        <input type="file" @change="guardarImagen" accept="image/*" class="input-file-oculto">
                        <div class="diseno-upload">
                            <span class="icono-nube">↑</span>
                            <p>Haz clic para subir o arrastra una imagen</p>
                            <small>PNG, JPG o WEBP (máx. 5MB)</small>
                        </div>
                    </div>
                </div>

                <div class="banner-informativo">
                    <p><strong>Nota:</strong> Una vez publicado, los compradores podrán ver tu producto y enviarte
                        solicitudes de compra.</p>
                </div>

                <div class="acciones">
                    <button type="button" class="boton-cancelar" @click="$router.go(-1)">Cancelar</button>
                    <button type="submit" class="boton-publicar" :disabled="formularioIncompleto">Publicar
                        producto</button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', 'Arial';
}

body {
    min-width: 400px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    min-width: 400px;
}

.contenedor-pagina {
    margin-top: 80px;
    padding: 20px 50px;
}

#contenedor-titulo {
    max-width: 90%;
    margin: 40px auto 0 auto;
}

.titulo {
    font-family: sans-serif;
    color: #4CA626;
    margin-bottom: 10px;
    font-weight: bold;
}

.subtitulo {
    font-family: sans-serif;
    color: #666666;
    margin-bottom: 20px;
}

.tarjeta-formulario {
    background: #FFFFFF;
    width: 100%;
    max-width: 650px;
    padding: 35px;
    border-radius: 12px;
    border: 1px solid #EDF2F7;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    margin: 0 auto;
}

.header-interno {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 25px;
    color: #1A202C;
}

label {
    display: block;
    font-size: 13.5px;
    font-weight: 500;
    color: #4A5568;
    margin-bottom: 8px;
}

input[type="text"],
input[type="number"],
select,
textarea {
    width: 100%;
    padding: 11px 14px;
    background-color: #F8FAFC;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 18px;
    transition: all 0.2s ease;
    font-family: inherit;
    box-sizing: border-box;
}

input:focus,
select:focus,
textarea:focus {
    outline: none;
    border-color: #00A859;
    box-shadow: 0 0 0 3px rgba(0, 168, 89, 0.1);
    background-color: #FFFFFF;
}

.fila-doble {
    display: flex;
    gap: 60px;
    width: 100%;
}

.columna {
    flex: 1;
}

.ayuda-texto {
    font-size: 12px;
    color: #A0AEC0;
    margin-top: -12px;
    margin-bottom: 15px;
}

.error-texto {
    font-size: 12px;
    color: #e53e3e;
    margin-top: -6px;
    margin-bottom: 15px;
}

.zona-upload {
    position: relative;
    border: 2px dashed #E2E8f0;
    border-radius: 10px;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    margin-bottom: 25px;
    background-color: #fff;
    transition: all 0.2s;
}

.zona-upload:hover {
    background-color: #f9fafb;
    border-color: #00a859;
}

.input-file-oculto {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
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

/* NOTA AZUL */
.banner-informativo {
    background-color: #F0F7FF;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 30px;
}

.banner-informativo p {
    color: #007BFF;
    font-size: 12.5px;
    line-height: 1.5;
}

.banner-informativo strong {
    color: #1e3a8a;
}

.acciones {
    display: flex;
    gap: 20px;
    width: 100%;
}

button {
    flex: 1;
    padding: 14px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.boton-cancelar {
    background: white;
    border: 1px solid #E2E8f0;
    color: #4A5568;
}

.boton-publicar {
    background: linear-gradient(90deg, #4CA626 0%, #009B58 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
}

.boton-cancelar:hover {
    background: linear-gradient(90deg, #FF6F6F 0%, #FF2C2C 100%);
    color: white;
}

.boton-publicar:hover:not(:disabled) {
    background: linear-gradient(to right, #00A650, #008F44);
}

.boton-publicar:disabled {
    background: #CBD5E0;
    background-image: none;
    cursor: not-allowed;
    color: #FFFFFF;
}
</style>