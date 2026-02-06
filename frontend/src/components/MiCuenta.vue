<script setup>
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { ref, nextTick, onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth.js';
import axios from 'axios'
import NavBar from './NavBar.vue'
import MostrarProductos from './MostrarProductos.vue'

let map;
const activarMapa = ref(false)
const direccion = ref('');
const latitud = ref(null)
const longitud = ref(null)
const nombrePunto = ref('')
const puntosEntrega = ref([])
const { usuario, fetchUsuario } = useAuth();
const productosUser = ref([])
const eleccionActual = ref('productos');

// console.log(puntosEntrega)

const guardarPuntoEntrega = async () => {
    activarMapa.value = true;

    if (map) {
        map.remove();
    }

    await nextTick();

    if (usuario.value.latitud && usuario.value.longitud) {
        latitud.value = usuario.value.latitud;
        longitud.value = usuario.value.longitud;
        direccion.value = usuario.value.direccion;
    }

    const centroInicial = (latitud.value && longitud.value)
        ? [latitud.value, longitud.value]
        : [39.032719, -0.215864];

    direccion.value = "";
    nombrePunto.value = "";
    latitud.value = null;
    longitud.value = null;

    map = L.map('map', {
        miZoom: 3,
    }).setView(centroInicial, 8); // empezar con la localización del usuario

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        minZoom: 3,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    for (let i = 0; i < puntosEntrega.value.length; i++) {
        const longitud = parseFloat(puntosEntrega.value[i].longitud)
        const latitud = parseFloat(puntosEntrega.value[i].latitud)
        L.marker([latitud, longitud]).addTo(map).bindPopup(puntosEntrega.value[i].nombre_punto);
    }

    let marcadorTemporal = L.marker(centroInicial, { opacity: 0 }).addTo(map);

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

            // los optional chaining para que no se rompa si se hace click en el mar
            direccion.value = [
                address?.road,
                address?.city || address?.town || address?.village,
                address?.postcode,
                address?.country
            ]

            direccion.value = direccion.value.filter(Boolean).join(", ");
            console.log("Valor de direccion:", `"${direccion.value}"`);
            marcadorTemporal
                .setLatLng(e.latlng)
                .setOpacity(1)
                .bindPopup("Ubicación seleccionada")
                .openPopup();

        } catch (error) {
            alert("Punto no válido.")
            console.error(error.message);
        }
    }
    map.off('click');
    map.on('click', onMapClick);
}

const crearPunto = async () => {
    const token = localStorage.getItem('token');
    const datos = {
        latitud: latitud.value,
        longitud: longitud.value,
        nombre_punto: nombrePunto.value,
        direccion_punto: direccion.value
    }
    try {
        const response = await axios.post('http://localhost:8080/api/puntos', datos, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

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
    const token = localStorage.getItem('token');
    const resposta = await axios.get(`http://localhost:8080/api/usuarios/${usuario.value.id}/puntos`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    })
    puntosEntrega.value = resposta.data;
}

const esconderMapa = () => {
    activarMapa.value = false
}

const eliminarPunto = async (id) => {
    if (!confirm('¿Estás seguro de que quieres eliminar este punto de entrega?')) return;

    const token = localStorage.getItem('token');
    try {
        await axios.delete(`http://localhost:8080/api/puntos/${id}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        alert('Punto eliminado correctamente');
        puntosEntrega.value = puntosEntrega.value.filter(p => p.id !== id);
        if (activarMapa.value) {
            guardarPuntoEntrega();
        }
    } catch (error) {
        alert('No se pudo eliminar el punto.');
        console.error("Error al eliminar:", error);
    }
}

const cargarProductosUser = async () => {
    const token = localStorage.getItem('token');
    const productos = await axios.get(`http://localhost:8080/api/usuarios/${usuario.value.id}/productos`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });

    productosUser.value = productos.data;
}

const eliminarProducto = async (id) => {
    const token = localStorage.getItem('token');

    const response = await axios.delete('http://localhost:8080/api/productos/' + id, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });

    if (response.status === 200) {
        alert('Producto eliminado correctamente');
        productosUser.value = productosUser.value.filter(p => p.id !== id);
    }
}

const irAlPunto = (punto) => {
    const lat = parseFloat(punto.latitud);
    const lng = parseFloat(punto.longitud);
    map.flyTo([lat, lng], 10);
}

onMounted(async () => {
    if (!usuario.value?.id) await fetchUsuario();
    cargarPuntos();
    cargarProductosUser();
});

</script>
<template>
    <NavBar/>
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
                                class="icono">Nombre:</span> {{ usuario.nombre_usuario || 'Cargando...' }}</p>
                    <p><span><img src="../assets/iconos/correo.png" alt="icono-email" class="icono">Email:</span> {{
                        usuario.email }}</p>
                    <p><span><img src="../assets/iconos/ubicacion.png" alt="icono-direccion"
                                class="icono">Dirección:</span> {{ usuario.direccion || 'No definida' }}</p>
                    <hr>
                    <p class="valoracion"><span><img src="../assets/iconos/valoraciones-icono.png"
                                alt="icono-valoracion" class="icono">Valoración:</span> <span class="puntuacion">{{
                                    usuario.puntuacio || '5.0' }}</span></p>
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
                    @borrar="eliminarProducto" />
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
    border-radius:
        8px;
    margin-bottom:
        15px;
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