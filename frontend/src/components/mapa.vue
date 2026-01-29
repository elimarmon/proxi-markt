<template>
  <navbar></navbar>
  <div class="contenedor-pagina">
    <div id="contenedor-titulo">
      <h1 class="titulo">Mapa</h1>
      <p class="subtitulo">
        Visualiza los puntos de entrega y productos
      </p>
    </div>

    <div id="contenedor-mapa">
      <div id="map" class="leaflet-map"></div>
    </div>
    <div class="productos">
      <MostrarProductosMain :productos="productos"></MostrarProductosMain>
    </div>
  </div>
</template>

<script setup>
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { ref, onMounted, nextTick } from "vue";
import axios from "axios";
import navbar from "./nav.vue";
import MostrarProductosMain from './MostrarProductosMain.vue';

let map = null;
const puntosdeproductos = ref([]);
const productos = ref([]);

const cargarMarcadores = () => {
  if (!map) return;
  const token = localStorage.getItem('token');

  puntosdeproductos.value.forEach(punto => {
    const marker = L.marker([punto.latitud, punto.longitud]).addTo(map);

    marker.on('click', async () => {
      const response = await axios.get('http://localhost:8080/api/productosporpunto/' + punto.id, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });

      productos.value = response.data.productos;
      console.log("Productos del punto ", productos.value)

    });

    marker.bindTooltip(punto.nombre_punto);
  });
};

const obtenerpuntos = async () => {
  await nextTick();

  const radioususario = localStorage.getItem('distancia_guardada');
  const token = localStorage.getItem('token');

  const datosuser = await axios.get('http://localhost:8080/api/datosuser', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  })

  const { longitud: lng, latitud: lat } = datosuser.data;

  map = L.map('map').setView([lat, lng], 13);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  const puntosdeentrega = await axios.get('http://localhost:8080/api/puntos_radio/' + radioususario, {
    params: { lng, lat },
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  puntosdeproductos.value = puntosdeentrega.data;

  cargarMarcadores();
}

onMounted(() => {
  obtenerpuntos();
})
</script>

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

#map {
  height: 500px;
  width: 90%;
  margin: 0 auto;
  border-radius: 12px;
  border: 2px solid #4ca626;
  z-index: 1;
  /* Para que no se solape con la navbar */
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
  color: #4ca626;
  margin-bottom: 10px;
  font-weight: bold;
}

.subtitulo {
  font-family: sans-serif;
  color: #666666;
  margin-bottom: 20px;
}

#contenedor-mapa {
  max-width: 90%;
  margin-left: auto;
  margin-right: auto;
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}


</style>