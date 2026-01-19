<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import navbar from "./nav.vue";
// import TarjetaProducto from "@/components/TarjetaProducto.vue";
import MostrarProductos from './MostrarProductosMain.vue'

const productos = ref([]);

const mostrarProductos = async () => {
    const response = await axios.get("http://localhost:8080/api/productos");
    productos.value = response.data;
};

onMounted(() => {
    mostrarProductos();
});
</script>

<template>
  <navbar></navbar>
  <div class="contenedor-pagina">
    <h1 class="titulo-verde">Productos Frescos y Locales</h1>
    <p class="subtitulo">Conecta directamente con productores de tu zona (radio: 50 km)</p>

    <div class="card-busqueda">
      <div id="buscador">
        <div class="caja-busqueda">
          <img src="../assets/iconos/buscar.png" alt="lupa" class="icono-pequeno" />
          <input class="input-texto" type="text" placeholder="Buscar productos frescos..."/>
        </div>
        <button class="btn-secundario">
          <img src="../assets/iconos/filtro.png" alt="filtro" class="icono-pequeno"/>
          Filtros
        </button>
      </div>
      <p class="informacion-resultados"> 
        6 productos encontrados <span class="texto-verde">(en un radio de 50 km)</span>
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
  color: #4ca626; 
  font-size: 2rem; 
  margin-bottom: 5px; 
  font-weight: bold;
}

.subtitulo { 
  color: #666; 
  margin-bottom: 30px; 
}

.card-busqueda {
  background: white;
  border: 1px solid #eee;
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
  background-color: #f9f9f9;
  border-radius: 8px;
  padding: 12px 15px;
  display: flex;
  align-items: center;
  flex-grow: 1;
  border: 1px solid #ddd;
}

.input-texto {
  border: none;
  background: transparent;
  width: 100%;
  margin-left: 10px;
  outline: none;
  font-size: 16px;
  color: #333;
}

.btn-secundario {
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 12px 20px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: bold;
  color: #333;
}

.btn-secundario:hover {
  background-color: #f5f5f5;
}

.icono-pequeno {
  width: 20px;
  height: 20px;
}

.informacion-resultados {
  font-size: 0.9rem;
  color: #666;
  padding-left: 5px;
}

.texto-verde {
  color: #4ca626;
  font-weight: 500;
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
