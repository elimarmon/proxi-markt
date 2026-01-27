<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import navbar from "./nav.vue";
// import TarjetaProducto from "@/components/TarjetaProducto.vue";
import MostrarProductos from './MostrarProductosMain.vue'

const productos = ref([]);
const radioActual = ref(Number(localStorage.getItem('distancia_guardada')) || 10);

const manejarCambioRadio = (nuevoRadio) => {
    radioActual.value = nuevoRadio;
    mostrarProductos();
};

const mostrarProductos = async () => {
    const response = await axios.get("http://localhost:8080/api/productos", {
      params: {
        km: radioActual.value
      }
    });
    productos.value = response.data;
    console.log(response);
};

onMounted(() => {
    mostrarProductos();
});
</script>

<template>
  <navbar @cambiar-radio="manejarCambioRadio"></navbar>
  
  <div class="contenedor-pagina">
    <h1 class="titulo-verde">Productos Frescos y Locales</h1>
    <p class="subtitulo">Conecta directamente con productores de tu zona (radio: {{ radioActual }} km)</p>

    <div class="card-busqueda">
      <div id="buscador">
        <div class="caja-busqueda">
          <img src="../assets/iconos/buscar.png" alt="lupa" class="icono-pequeno" />
          <input class="input-texto" type="text" placeholder="Buscar productos frescos..."/>
        </div>
        <button class="boton-secundario">
          <img src="../assets/iconos/filtro.png" alt="filtro" class="icono-pequeno"/>
          Filtros
        </button>
      </div>
      <p class="informacion-resultados"> 
        {{ productos.length }} productos encontrados <span class="texto-verde">(en un radio de {{ radioActual }} km)</span>
      </p>
    </div>

    <MostrarProductos :productos="productos"></MostrarProductos>
    
  </div>
</template>

<style scoped>
.contenedor-pagina {
  max-width: 1200px;
  margin: 90px auto 0;
  padding: 0 40px 40px 40px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.titulo-verde { 
  color: #4CA626; 
  font-size: 2rem; 
  margin-bottom: 5px; 
  font-weight: bold;
}

.subtitulo { 
  color: #666666; 
  margin-bottom: 30px; 
}

.card-busqueda {
  background: white;
  border: 1px solid #EEEEEE;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 40px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

#buscador {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 10px;
}

.caja-busqueda {
  background-color: #F9F9F9;
  border-radius: 8px;
  padding: 12px 15px;
  display: flex;
  align-items: center;
  flex-grow: 1;
  border: 1px solid #DDDDDD;
}

.input-texto {
  border: none;
  background: transparent;
  width: 100%;
  margin-left: 10px;
  outline: none;
  font-size: 16px;
  color: #333333;
}

.boton-secundario {
  background-color: white;
  border: 1px solid #DDDDDD;
  border-radius: 8px;
  padding: 12px 20px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: bold;
  color: #333333;
}

.boton-secundario:hover {
  background-color: #F5F5F5;
}

.icono-pequeno {
  width: 20px;
  height: 20px;
}

.informacion-resultados {
  font-size: 0.9rem;
  color: #666666;
  padding-left: 5px;
}

.texto-verde {
  color: #4CA626;
}

@media (max-width: 768px) {
  .contenedor-pagina {
    padding: 0 20px 20px 20px;
  }
  #buscador {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>