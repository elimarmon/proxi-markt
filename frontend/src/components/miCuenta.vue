<script setup>
  import L from 'leaflet'
  import 'leaflet/dist/leaflet.css'
  import { ref, nextTick, onMounted } from 'vue'
  import axios from 'axios'
  import navbar from './nav.vue'
  import MostrarProductos from './mostrarProductos.vue'
  import { useRouter } from "vue-router";

  const router = useRouter();

  let map;

  const activarMapa = ref(false)

  const nombreCalle = ref('');
  const latitud = ref(0)
  const longitud = ref(0)
  const nombrePunto = ref('')
  const PuntosEntrega = ref([])
  const DatosUser = ref([])
  const ProductosUser = ref([])

  console.log(PuntosEntrega)

  const GuardarPuntoEntrega = async () => {
        activarMapa.value = true;
    

    await nextTick();
    
    map = L.map('map').setView([39.032719, -0.215864], 13); 

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
      }).addTo(map);
    
    for(let i = 0 ; i < PuntosEntrega.value.length ; i++){
      const longitud = parseFloat(PuntosEntrega.value[i].longitud)
      const latitud = parseFloat(PuntosEntrega.value[i].latitud)
      const marker = L.marker([ latitud, longitud ]).addTo(map).bindPopup( PuntosEntrega.value[i].nombre_punto );
    }
    
    var markerseleccion = L.marker([0, 0]).addTo(map);

    async function onMapClick(e) {
      latitud.value = e.latlng.lat;
      longitud.value = e.latlng.lng;

      const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitud.value}&lon=${longitud.value}`;
      try{
        const response = await fetch(url);
        const data = await response.json();

        console.log(data);

        nombreCalle.value = data.address.road || data.display_name

        markerseleccion
          .setLatLng(e.latlng) 
          .bindPopup("Estas en " + nombreCalle.value) 
          .openPopup();
        }catch(error){

        }
    }
    map.on('click', onMapClick);

    }

  const CrearPunto = async () =>{
    const token = localStorage.getItem('token');
    const Datos = {
        latitud: latitud.value,
        longitud: longitud.value,
        nombre_punto: nombrePunto.value,
        direccion_punto: nombreCalle.value
    }
    try{
        await axios.post('http://localhost:8080/api/insertarpunto', Datos, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        alert('creado')
        location.reload();
    }catch (error){
        console.error("Error del servidor:", error.response ? error.response.data : error.message);
        
        const mensajeError = error.response?.data?.message || "Error desconocido";
        alert('Fallo al crear: ' + mensajeError);
    }
  }

  const CargarPuntos = async() => {
    const token = localStorage.getItem('token');
    const resposta = await axios.get('http://localhost:8080/api/puntosuser', {
      headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
    })
    PuntosEntrega.value = resposta.data;
  }

  const EsconderMapa = () =>{
    activarMapa.value = false
  }

  const DatosUsuario = async() =>{
    const token = localStorage.getItem('token');
    const Usuario = await axios.get('http://localhost:8080/api/datosuser',{
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
    })
    DatosUser.value = Usuario.data;
    console.log("Datos del usuario",DatosUser.value)
  }

    const EliminarPunto = async (id) => {
        if (!confirm('¿Estás seguro de que quieres eliminar este punto de entrega?')) return;

        const token = localStorage.getItem('token');
        try {
            await axios.delete(`http://localhost:8080/api/deletepunto/${id}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            alert('Punto eliminado correctamente');
            PuntosEntrega.value = PuntosEntrega.value.filter(p => p.id !== id);
            location.reload();
        } catch (error) {
            console.error("Error al eliminar:", error);
            alert('No se pudo eliminar el punto');
        }
    }

    const CargarProductosUser = async () => {
      const token = localStorage.getItem('token');
      const productos = await axios.get('http://localhost:8080/api/productosuser', {
        headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json'
            }}
          )
      
      ProductosUser.value = productos.data;

  }

    const EliminarProducto = async (id) => {
      const token = localStorage.getItem('token');
      
      const eliminar = await axios.delete('http://localhost:8080/api/productos/'+ id, {
        headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json'
            }}
          )
      if(eliminar.status === 200){
        alert('producto eliminado correctamente')
        location.reload();
      }
    }

  onMounted(() => {
      CargarPuntos();
      DatosUsuario();
      CargarProductosUser();
  });

</script>
<template>
  <navbar></navbar>
  <div class="contenedor-pagina">
    <div class="contenedor-titulo">
      <h1 class="titulo">Mi Cuenta</h1>
      <p class="subtitulo">Gestiona tus productos y datos personales</p><br>
    </div>

    <div class="contenido-centrado">
      <div class="card-perfil">
        <h3>Mi Perfil</h3><br>
        <div class="info-usuario">
          <p><span><img src="../assets/iconos/mi_cuenta_verde.png" alt="icono-usuario" class="icono">Nombre:</span> {{ DatosUser.nombre_usuario || 'Cargando...' }}</p>
          <p><span><img src="../assets/iconos/correo.png" alt="icono-email" class="icono">Email:</span> {{ DatosUser.email }}</p>
          <p><span><img src="../assets/iconos/ubicacion.png" alt="icono-direccion" class="icono">Dirección:</span> {{ DatosUser.direccion || 'No definida' }}</p>
          <hr>
          <p class="valoracion"><span><img src="../assets/iconos/valoraciones-icono.png" alt="icono-valoracion" class="icono">Valoración:</span> <span class="puntuacion">{{ DatosUser.puntuacio || '5.0' }}</span></p>
        </div>
      </div>

      <div class="contenedor-accion-superior">
        <button @click="GuardarPuntoEntrega" class="botones-perfil">
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
              <button @click="CrearPunto" class="boton-confirmar">Guardar</button>
              <button @click="EsconderMapa" class="boton-cancelar">Cerrar</button>
            </div>
          </div>
        </div>

        <div class="tus-puntos-existentes">
          <h3>Tus puntos de entrega actuales</h3>
          <div class="grid-puntos-mini">
            <div v-for="punto in PuntosEntrega" :key="punto.id" class="card-punto-mini">
              <div class="info-mini">
                <strong>{{ punto.nombre_punto }}</strong>
                <p>{{ punto.direccion_punto }}</p>
              </div>
              <button @click="EliminarPunto(punto.id)" class="boton-borrar">Borrar</button>
            </div>
          </div>
        </div>
      </div>

    <div class="contenedor-secciones-datos">
      
      <section class="seccion-bloque">
        <h3>Mis productos</h3>
        <MostrarProductos :productos="ProductosUser" @borrar="EliminarProducto"></MostrarProductos>
      </section>

        <section class="seccion-bloque">
          <h3>Mis Compras</h3>
          <div class="card-vacia">No has realizado compras.</div>
        </section>

        <section class="seccion-bloque">
          <h3>Mis Ventas</h3>
          <div class="card-vacia">No tienes ventas registradas.</div>
        </section>

        <section class="seccion-bloque">
          <h3>Mis Valoraciones</h3>
          <div class="card-vacia">Aún no tienes valoraciones.</div>
        </section>

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

.contenedor-titulo{
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

.info-usuario .valoracion {
  margin-bottom: 0;
}

.info-usuario p span.puntuacion {
  color: black;
  font-weight: normal;
  font-size: 1.2rem;
}

.info-usuario p{
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
    8px; margin-bottom: 
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

.boton-cancelar { 
    background: #ccc; 
    color: white; 
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

@media (max-width: 600px) {
  .contenedor-accion-superior {
    flex-direction: column;
  }
}
</style>