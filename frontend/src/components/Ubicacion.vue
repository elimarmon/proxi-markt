<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';

const router = useRouter()

const { usuario, fetchUsuario } = useAuth();
const latitud = ref(null);
const longitud = ref(null);
const direccion = ref('');
const cargando = ref(false);

let map = null;
let markerseleccion = null;

const inicializarMapa = async () => {

    if (usuario.value.latitud && usuario.value.longitud) {
        latitud.value = usuario.value.latitud;
        longitud.value = usuario.value.longitud;
        direccion.value = usuario.value.direccion;
    }

    const centroInicial = (latitud.value && longitud.value)
        ? [latitud.value, longitud.value]
        : [39.032719, -0.215864];

    await nextTick();

    map = L.map('map', {
        minZoom: 3,
    }).setView(centroInicial, 13);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        minzoom: 3,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    markerseleccion = L.marker(centroInicial).addTo(map);

    map.on('click', async (e) => {
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

            markerseleccion
                .setLatLng(e.latlng)
                .bindPopup("Ubicación seleccionada")
                .openPopup();

        } catch (error) {
            console.error("Error al obtener dirección:", error);
            direccion.value = "Dirección no encontrada";
        }
    });
};

const guardarUbicacion = async () => {
    const token = localStorage.getItem('token');

    if (!token) {
        alert("Sesión no válida. Por favor, inicia sesión.");
        return;
    }

    cargando.value = true;

    const datos = {
        direccion: direccion.value,
        latitud: latitud.value,
        longitud: longitud.value
    };

    try {
        const respuesta = await axios.put(`http://localhost:8080/api/usuarios/${usuario.value.id}/ubicacion`, datos, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (respuesta.status >= 200 && respuesta.status < 300) {
            // actualitzar l'objecte usuari manualment per no fer una nova petició a la base de dates
            usuario.value = {
                ...usuario.value,
                ...datos
            };
            alert("Dirección actualizada correctamente.");
            // console.log("Respuesta:", respuesta.data);
            router.push('/cuenta');
        }

    } catch (error) {
        console.error("Error al guardar:", error.response?.data);
        alert("Hubo un error al guardar los datos.");
    } finally {
        cargando.value = false;
    }
};

const cancelar = () => {
    if (direccion.value && direccion.value !== usuario.value.direccion) {
        if (confirm("Tienes cambios sin guardar. ¿Quieres salir?")) {
            router.push('/cuenta');
        }
    } else {
        router.push('/cuenta');
    }
}

onMounted(async () => {
    if (!usuario.value?.id) await fetchUsuario();
    inicializarMapa();
});
</script>
<template>
    <div class="main-container">
        <div class="card">
            <h2 class="title">Configuración de Ubicación</h2>

            <div class="map-section">
                <div id="map" class="leaflet-map"></div>
            </div>

            <div class="status-section">
                <div v-if="direccion" class="info-box animate-in">
                    <p class="label">Dirección seleccionada:</p>
                    <p class="address-text">{{ direccion }}</p>

                    <div class="button-group">
                        <button @click="cancelar" :disabled="cargando" class="boton-secondary">
                            Cancelar
                        </button>

                        <button @click="guardarUbicacion" :disabled="cargando || !latitud" class="boton-primary">
                            <span v-if="!cargando">Confirmar y Guardar</span>
                            <span v-else>Procesando...</span>
                        </button>
                    </div>
                </div>

                <div v-else class="empty-state">
                    <p>Haz clic en el mapa para marcar tu posición en el mapa</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.main-container {
    min-height: 100vh;
    background-color: #F0F0F0;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.card {
    background-color: #FFFFFF;
    width: 100%;
    max-width: 700px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    padding: 24px;
}

.title {
    color: #4CA626;
    margin-bottom: 20px;
    font-weight: 700;
    text-align: center;
}

.leaflet-map {
    height: 350px;
    width: 100%;
    border-radius: 12px;
    border: 2px solid #F0F0F0;
}

.status-section {
    margin-top: 20px;
}

.info-box {
    background-color: #B9E2A6;
    padding: 15px;
    border-radius: 10px;
    border: 1px solid #8BD16A;
}

.label {
    color: #2c5d18;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.address-text {
    color: #333333;
    font-weight: 500;
    margin-bottom: 15px;
}

.button-group {
    display: flex;
    gap: 12px;
    margin-top: 15px;
}

.boton-primary {
    flex: 2;
    background-color: #4CA626;
    color: white;
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s ease;
}

.boton-primary:hover {
    background-color: #8BD16A;
}

.boton-primary:disabled {
    background-color: #B9E2A6;
    cursor: not-allowed;
}

.boton-secondary {
    flex: 1;
    background-color: #f0f7ed;
    color: #4CA626;
    border: 1px solid #c2e0b5;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.boton-secondary:hover {
    background-color: #e2f0da;
    border-color: #8BD16A;
}

.boton-secondary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.empty-state {
    text-align: center;
    color: #8BD16A;
    font-style: italic;
    padding: 10px;
}

.animate-in {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>