<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import ModalRadio from './ModalRadio.vue';
import axios from 'axios';

const router = useRouter();
const mostrarMenu = ref(false);

const cerrarSesion = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('datos_usuario');
  router.push('/login');
};

const isModalOpen = ref(false);
const emit = defineEmits(['cambiar-radio']);

const DatosUser = ref({});
const radioActual = ref(Number(localStorage.getItem('distancia_guardada')) || 10);

const confirmarNuevoRadio = (valor) => {
  radioActual.value = valor;
  localStorage.setItem('distancia_guardada', valor);
  isModalOpen.value = false;
  emit('cambiar-radio', valor);
  console.log("Filtrando productos a:", valor, "km");
};

const nombreUsuario = async () => {
  const token = localStorage.getItem('token');
  
  try {
    const respuesta = await axios.get('http://localhost:8080/api/datosuser', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    
    DatosUser.value = respuesta.data;

  } catch (error) {
    console.error("Error al obtener el nombre de usuario:", error);
  }
}

onMounted(() => {
  nombreUsuario();
});
</script>

<template>
  <header>
    <div id="nav-contenedor">
      <div id="logo">
        <img src="../assets/logos/logo_peq.png" alt="Logo ProxiMarkt"/>
        <p class="titulo">ProxiMarkt</p>
        <p class="subtitulo">Frutas y verduras frescas</p>
      </div>
      <nav>
        <ul>
          <li>
            <router-link to="/dashboard">
                <img class="logos-nav" src="../assets/iconos/dashboard_verde.png" alt="logo_dashboard">
                Dashboard
            </router-link>
          </li>
          <li>
            <router-link to="/productos">
                <img class="logos-nav" src="../assets/iconos/productos_verde.png" alt="logo_productos">
                Productos
            </router-link>
          </li>
          <li>
            <router-link to="/mapa">
                <img class="logos-nav" src="../assets/iconos/ubicacion.png" alt="logo_mapa">
                Mapa
            </router-link>
          </li>
          <li>
            <router-link to="/mensaje">
                <img class="logos-nav" src="../assets/iconos/chat_verde.png" alt="logo_mensajes">
                Mensajes
            </router-link>
          </li>
          <li>
            <router-link to="/comandas">
                <img class="logos-nav" src="../assets/iconos/comandas.png" alt="logo_comandas">
                Comandas
            </router-link>
          </li>
          <li>
            <router-link to="/publicar">
                <img class="logos-nav" src="../assets/iconos/publicar_verde.png" alt="logo_publicar">
                Publicar</router-link></li>
          <li>
            <router-link to="/cuenta">
                <img class="logos-nav" src="../assets/iconos/mi_cuenta_verde.png" alt="logo_mi_cuenta">
                Mi cuenta
            </router-link>
          </li>
        </ul>
      </nav>

      <div id="radio_busqueda" @click="isModalOpen = true" style="cursor: pointer;">
        <p>
            <img src="../assets/iconos/tuerca_verde.png" alt="logo_tuerca">
            {{ radioActual }}km
        </p>
      </div>

      <div class="contenedor-perfil">
        
        <div id="usuario" @click="mostrarMenu = !mostrarMenu">
          <img src="../assets/iconos/cuenta.png" alt="icono_perfil">
          {{ DatosUser.nombre_usuario }}
          <span class="triangulo"> ▼ </span>
        </div>

        <div v-if="mostrarMenu" class="menu-desplegable">
          <ul>
            <li @click="cerrarSesion">
              <img src="../assets/iconos/rechazar.png" alt="cerrar sesión" class="icono-salir"> Cerrar sesión
            </li>
          </ul>
        </div>
      </div> </div>  <ModalRadio 
      :mostrar="isModalOpen" 
      :distanciaInicial="radioActual"
      @cerrar="isModalOpen = false" 
      @confirmar="confirmarNuevoRadio"
    />
  </header>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', 'Arial';
}

header {
  width: 100%;
  background-color: #ffffff;
  box-shadow: 0px 4px 15px -5px #CFCFCF;
  height: 80px;
  display: flex;
  align-items: center;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1000;
}

#nav-contenedor {
  width: 100%;
  padding: 0 30px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

#logo {
  display: grid;
  grid-template-columns: min-content max-content;
  grid-template-rows: auto auto;
  column-gap: 12px;
  align-items: center;
  margin-right: 40px;
}

#logo img {
  grid-row: 1 / 3; 
  height: 50px;
  width: auto;
  border-radius: 12px;
}

.titulo {
  grid-column: 2;
  font-size: 25px;
  font-weight: 600;
  color: #4CA626;
  line-height: 1.2;
}

.subtitulo {
  grid-column: 2;
  font-size: 15px;
  color: #757575;
  line-height: 1.2;
}

nav {
  display: flex;
  justify-content: center;
  flex: 1;
}

nav ul {
  display: flex;
  list-style: none;
  align-self: flex-start;
  gap: 5px;
  margin-left: 50px;
  align-items: center;
}

nav li a {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: #5F6368;
  font-size: 18px;
  font-weight: 500;
  padding: 10px 16px;
  border-radius: 10px;
}

nav li a .logos-nav {
  width: 30;
  height: 30px;
  margin-right: 10px;
}

nav li a:hover {
  background-color: #4CA6264A;
  color: #4CA626;
}

nav li a:hover .logos-nav {
  filter: none;
  opacity: 1;
}

.router-link-active,
.router-link-exact-active {
  background-color: #4CA6264A;
  color: #4CA626;
}

#radio_busqueda {
  margin-right: 25px;
}

#radio_busqueda p {
  display: flex;
  align-items: center;
  color: #5F6368;
  font-size: 14px;
  font-weight: 600;
  gap: 8px;
  cursor: pointer;
}

#radio_busqueda img {
  width: 40px;
  height: 40px;
}

#usuario {
  display: flex;
  align-items: center;
  background-color: #4CA6264A;
  color: #757575;
  padding: 6px 20px 6px 6px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 600;
}

#usuario img {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  margin-right: 10px;
  object-fit: cover;
}

.contenedor-perfil {
  position: relative; 
  cursor: pointer;
}

.menu-desplegable {
  position: absolute;
  top: 100%;
  right: 0;
  width: 180px;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);
  margin-top: 10px;
  padding: 10px 0;
  z-index: 2000;
  overflow: hidden;
  border: 1px solid #EEEEEE;
}

.menu-desplegable ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu-desplegable li {
  padding: 12px 20px;
  display: flex;
  align-items: center;
  gap: 10px;
  color: #5F6368;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
  font-size: 14px;
}

.menu-desplegable li:hover {
  background-color: #FFECEC;
  color: #D32F2F;
}

.icono-salir {
  width: 18px;
  height: 18px;
  opacity: 0.6;
}

.triangulo {
  font-size: 10px;
  margin-left: 5px;
}

@media (max-width: 1200px) {
  #nav-contenedor {
    padding: 0 15px;
  }

  nav li a {
    font-size: 0;
    padding: 10px;
  }

  nav li a .logos-nav {
    margin-right: 0;
    width: 24px;
    height: 24px;
  }

  nav ul {
    margin-left: 20px;
    gap: 15px;
  }
}

@media (max-width: 768px) {
  header {
    height: auto;
    padding: 10px 0;
  }

  #nav-contenedor {
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 10px;
  }

  .titulo, .subtitulo {
    display: none;
  }

  #logo {
    margin-right: 0;
    display: flex;
  }

  #usuario {
    font-size: 16px;
    background-color: #4CA6264A;
    color: #757575;
    padding: 6px 20px 6px 6px;
    border-radius: 50px;
  }

  #usuario img {
    margin-right: 0;
    width: 40px;
    height: 40px;
  }

  #radio_busqueda {
    margin-right: 0;
    order: 2;
  }

  #radio_busqueda p {
    font-size: 16px;
  }

  nav {
    order: 3;
    flex-basis: 100%;
    overflow-x: auto;
    padding-bottom: 5px;
    margin-top: 5px;
  }

  nav ul {
    margin-left: 0;
    justify-content: flex-start;
    padding-left: 5px;
  }

  nav li a {
    background-color: #F5F5F5;
  }
}
</style>