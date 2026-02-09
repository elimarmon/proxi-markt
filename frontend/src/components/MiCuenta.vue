<script setup>
import L from 'leaflet'
import { ref, nextTick, onMounted, onUnmounted } from 'vue'
import { useAuth } from '@/composables/useAuth.js';
import axios from 'axios';
import api from '@/api/axios';
import NavBar from './NavBar.vue'
import MostrarProductos from './MostrarProductos.vue'
import MisVentas from './MisVentas.vue';

let map = null;
let layerPuntos = null;
let marcadorTemporal = null;
const activarMapa = ref(false)
const direccion = ref('');
const latitud = ref(null)
const longitud = ref(null)
const nombrePunto = ref('')
const puntosEntrega = ref([])
const { usuario, fetchUsuario } = useAuth();
const productosUser = ref([])
const eleccionActual = ref('productos');
const pagination = ref({});
const paginaActual = ref(1);

// console.log(puntosEntrega)

const guardarPuntoEntrega = async () => {
    activarMapa.value = true;

    const southWest = L.latLng(-89.9, -180);
    const northEast = L.latLng(89.9, 180);
    const bounds = L.latLngBounds(southWest, northEast);


    await nextTick();

    const centroInicial = (usuario.value?.latitud && usuario.value?.longitud)
        ? [usuario.value?.latitud, usuario.value?.longitud]
        : [39.032719, -0.215864];

    if (!map) {
        map = L.map('map', {
            minZoom: 3,
            worldCopyJump: true,
            maxBounds: bounds,
            maxBoundsViscosity: 1.0
        }).setView(centroInicial, 8);


        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            minZoom: 3,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        layerPuntos = L.layerGroup().addTo(map);
        marcadorTemporal = L.marker(centroInicial, { opacity: 0 }).addTo(map);
        map.on('click', onMapClick);
    }

    direccion.value = "";
    nombrePunto.value = "";
    longitud.value = null;
    latitud.value = null;

    cargarMarcadores();
}

const cargarMarcadores = () => {
    if (!layerPuntos) return;

    layerPuntos.clearLayers();

    puntosEntrega.value.forEach(punto => {
        const lat = parseFloat(punto.latitud);
        const lng = parseFloat(punto.longitud)
        L.marker([lat, lng])
            .addTo(layerPuntos)
            .bindPopup(punto.nombre_punto);
    });
}

async function onMapClick(e) {
    latitud.value = e.latlng.lat;
    longitud.value = e.latlng.lng;

    try {
        const response = await axios.get("https://nominatim.openstreetmap.org/reverse", {
            params: {
                format: 'jsonv2',
                lat: latitud.value,
                lon: longitud.value
            }
        });

        const address = response.data.address;

        direccion.value = [
            address.road,
            address.city || address.town || address.village,
            address.postcode,
            address.country
        ]

        direccion.value = direccion.value.filter(Boolean).join(", ");

        // console.log("Valor de direccion:", `"${direccion.value}"`);
        if (marcadorTemporal) {
            marcadorTemporal
                .setLatLng(e.latlng)
                .setOpacity(1)
                .bindPopup(direccion.value)
                .openPopup();
        }

    } catch (error) {
        alert("Punto no válido.")
        console.error(error.message);
    }
}

const crearPunto = async () => {
    const datos = {
        latitud: latitud.value,
        longitud: longitud.value,
        nombre_punto: nombrePunto.value,
        direccion_punto: direccion.value
    }
    try {
        const response = await api.post('/puntos', datos);

        const nuevoPunto = {
            id: response.data.id,
            nombre_punto: nombrePunto.value,
            direccion_punto: direccion.value,
            latitud: latitud.value,
            longitud: longitud.value
        };

        alert('Punto creado correctamente.')
        puntosEntrega.value.push(nuevoPunto);
    } catch (error) {
        console.error("Error del servidor:", error.response ? error.response.data : error.message);
        alert('Fallo al crear punto de entrega.');
    }
}

const cargarPuntos = async () => {
    const resposta = await api.get(`usuarios/${usuario.value.id}/puntos`);
    puntosEntrega.value = resposta.data;
}

const esconderMapa = () => {
    activarMapa.value = false
}

const eliminarPunto = async (id) => {
    if (!confirm('¿Estás seguro de que quieres eliminar este punto de entrega?')) return;

    try {
        await api.delete(`/puntos/${id}`);
        alert('Punto eliminado correctamente');
        puntosEntrega.value = puntosEntrega.value.filter(p => p.id !== id);
        if (activarMapa.value && map) {
            cargarMarcadores();
        }
    } catch (error) {
        alert('No se pudo eliminar el punto.');
        console.error("Error al eliminar:", error);
    }
}

const cargarProductosUser = async (pagina = 1) => {
    const productos = await api.get(`/usuarios/${usuario.value.id}/productos`, {
        params: {
            page: pagina
        }
    });

    productosUser.value = productos.data.data;
    pagination.value = productos.data;
    paginaActual.value = productos.data.current_page;
}

const eliminarProducto = async (id) => {
    await api.delete('/productos/' + id);
    alert('Producto eliminado correctamente');
    productosUser.value = productosUser.value.filter(p => p.id !== id);
}

const irAlPunto = (punto) => {
    const lat = parseFloat(punto.latitud);
    const lng = parseFloat(punto.longitud);
    map.flyTo([lat, lng], 10);
}

onMounted(async () => {
    await fetchUsuario();
    if (usuario.value?.id) {
        cargarPuntos();
        cargarProductosUser();
    }
});

onUnmounted(() => {
    if (map) {
        map.remove();
        map = null;
    }
});
</script>

<template>
    <NavBar />
    <div class="contenedor-pagina">
        <div class="contenedor-titulo">
            <h1 class="titulo">Mi Cuenta</h1>
            <p class="subtitulo">Gestiona tus productos y datos personales</p><br>
        </div>

        <div class="contenido-centrado">
            <div class="card-perfil">
                <h3>Mi Perfil</h3><br>
                <div class="info-usuario">
                    <p><span><img src="../assets/iconos/mi_cuenta_verde.png" alt="icono-usuario"
                                class="icono">Nombre:</span> {{ usuario?.nombre_usuario || 'Cargando...' }}</p>
                    <p><span><img src="../assets/iconos/correo.png" alt="icono-email" class="icono">Email:</span> {{
                        usuario?.email }}</p>
                    <p><span><img src="../assets/iconos/ubicacion.png" alt="icono-direccion"
                                class="icono">Dirección:</span> {{ usuario?.direccion || 'No definida' }}</p>
                    <hr>
                    <p class="valoracion"><span><img src="../assets/iconos/valoraciones-icono.png"
                                alt="icono-valoracion" class="icono">Valoración:</span> <span class="puntuacion">{{
                                    usuario?.puntuacio || '5.0' }}</span></p>
                </div>
            </div>

            <div class="contenedor-accion-superior">
                <button @click="guardarPuntoEntrega" class="botones-perfil">
                    Crear nuevo punto de entrega
                </button>
                <router-link to="/ubicacion" class="botones-perfil">
                    Cambiar mi ubicación
                </router-link>
            </div>

            <div v-if="activarMapa" class="seccion-gestion-puntos">
                <div class="formulario-mapa">
                    <h3>Configurar ubicación</h3>
                    <div id="map"></div>
                    <div class="controles-mapa">
                        <input v-model="nombrePunto" placeholder="Nombre del punto (Ej: Casa)">
                        <div class="botones-flex">
                            <button :disabled="!direccion" @click="crearPunto" class="boton-confirmar">Guardar</button>
                            <button @click="esconderMapa" class="boton-cancelar">Cerrar</button>
                        </div>
                    </div>
                </div>

                <div class="tus-puntos-existentes">
                    <h3>Tus puntos de entrega actuales</h3>
                    <div class="grid-puntos-mini">
                        <div v-for="punto in puntosEntrega" @click="irAlPunto(punto)" :key="punto.id"
                            class="card-punto-mini">
                            <div class="info-mini">
                                <strong>{{ punto.nombre_punto }}</strong>
                                <p>{{ punto.direccion_punto }}</p>
                            </div>
                            <button @click.stop="eliminarPunto(punto.id)" class="boton-borrar">Borrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="eleccion">
                <ul>
                    <li>
                        <button :class="{ active: eleccionActual === 'productos' }"
                            @click="eleccionActual = 'productos'">
                            <img src="../assets/iconos/productos_stock.png" alt="caja-stock" class="iconoSubNav">Mis
                            Productos ({{ productosUser.length }})
                        </button>
                    </li>
                    <li>
                        <button :class="{ active: eleccionActual === 'compras' }" @click="eleccionActual = 'compras'">
                            <img src="../assets/iconos/carrito.png" alt="carrito" class="iconoSubNav">Mis Compras
                        </button>
                    </li>
                    <li>
                        <button :class="{ active: eleccionActual === 'ventas' }" @click="eleccionActual = 'ventas'">
                            <img src="../assets/iconos/manzana.png" alt="manzana" class="iconoSubNav">Mis Ventas
                        </button>
                    </li>
                    <li>
                        <button :class="{ active: eleccionActual === 'valoraciones' }"
                            @click="eleccionActual = 'valoraciones'">
                            <img src="../assets/iconos/valoraciones-icono.png" alt="valoraciones"
                                class="iconoSubNav">Mis Valoraciones
                        </button>
                    </li>
                </ul>
            </div>

            <div class="contenedor-secciones-datos">
                <MostrarProductos v-if="eleccionActual === 'productos'" :productos="productosUser"
                    :pagination="pagination" :paginaActual="paginaActual" @borrar="eliminarProducto"
                    @cambiarPagina="cargarProductosUser" />
                <MisVentas v-else-if="eleccionActual === 'compras'" :eleccion="eleccionActual" />
                <MisVentas v-else-if="eleccionActual === 'ventas'" :eleccion="eleccionActual" />
            </div>
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

.contenedor-pagina {
    margin-top: 80px;
    padding: 20px 50px;
}

.contenido-centrado {
    max-width: 90%;
    margin: 0 auto;
}

.contenedor-titulo {
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

hr {
    border: none;
    height: 2px;
    background-color: #EEEEEE;
    margin-bottom: 10px;
}

.info-usuario p {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.info-usuario p span {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #4CA626;
    font-weight: bold;
    white-space: nowrap;
}

.icono {
    width: 25px;
    height: 25px;
}

.iconoSubNav {
    width: 25px;
    height: 25px;
    object-fit: contain;
}

.eleccion button {
    flex: 1;
    padding: 10px 0;
    border: none;
    font-weight: bold;
    font-size: 0.9rem;
    cursor: pointer;
    border-radius: 50px;
    color: #4b5563;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.info-usuario .valoracion {
    margin-bottom: 0;
}

.info-usuario p span.puntuacion {
    color: black;
    font-weight: normal;
    font-size: 1.2rem;
}

.info-usuario p {
    margin-bottom: 20px;
}

.card-perfil {
    background: white;
    border: 1px solid #eee;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.contenedor-accion-superior {
    margin-bottom: 40px;
}

.botones-perfil {
    background: linear-gradient(90deg, #4CA626 0%, #009B58 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
}

.botones-perfil:hover {
    background: linear-gradient(90deg, #008F4C 0%, rgb(1, 104, 59) 100%);
}

.seccion-gestion-puntos {
    margin-bottom: 40px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 12px;
    border: 1px solid #ddd;
}

#map {
    height: 300px;
    width: 100%;
    border-radius: 8px;
    margin-bottom: 15px;
    z-index: 1;
}

.grid-puntos-mini {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.card-punto-mini {
    background: white;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}

.card-punto-mini:hover {
    background-color: #f0fdf4;
    border-color: #4CA626;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.boton-borrar {
    background: #fee2e2;
    color: #ef4444;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
}

.boton-borrar:hover {
    background: #ef4444;
    color: white;
}

.contenedor-secciones-datos {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.seccion-bloque h3 {
    font-size: 1.1rem;
    margin-bottom: 10px;
    color: #333;
    padding-left: 5px;
}

.card-vacia {
    background: white;
    border: 1px solid #eee;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    color: #999;
    width: 100%;
}

.botones-flex {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
    align-self: flex-end;
}

.boton-confirmar {
    background: linear-gradient(90deg, #4CA626 0%, #009B58 100%);
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
}

.boton-confirmar:hover {
    background: linear-gradient(90deg, #008F4C 0%, rgb(1, 104, 59) 100%);
}

.boton-confirmar:disabled {
    background: #ccc;
    /* El color que tiene actualmente tu botón cancelar */
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.boton-cancelar {
    background: #fee2e2;
    color: #ef4444;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
}

.contenedor-accion-superior {
    margin-bottom: 40px;
    display: flex;
    gap: 15px;
}

.boton-ubicacion {
    background: linear-gradient(90deg, #4CA626 0%, #009B58 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s ease;
}

.boton-ubicacion:hover {
    background: linear-gradient(90deg, #008F4C 0%, rgb(1, 104, 59) 100%);
}

.eleccion {
    margin-bottom: 25px;
}

.eleccion ul {
    list-style: none;
    display: flex;
    background-color: #f3f4f6;
    padding: 5px;
    border-radius: 50px;
    border: 1px solid #e5e7eb;
    margin: 0;
}

.eleccion li {
    flex: 1;
    display: flex;
}

.eleccion button.active {
    background-color: white;
    color: #000;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.controles-mapa input {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 10px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    color: #374151;
    background-color: #ffffff;
    transition: all 0.3s ease;
    outline: none;
}

/* Efecto cuando el usuario va a escribir */
.controles-mapa input:focus {
    border-color: #4CA626;
    box-shadow: 0 0 0 3px rgba(76, 166, 38, 0.1);
}

/* Estilo para el placeholder (el texto de ayuda) */
.controles-mapa input::placeholder {
    color: #9ca3af;
}

@media (max-width: 600px) {
    .contenedor-accion-superior {
        flex-direction: column;
    }
}
</style>