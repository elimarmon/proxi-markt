<script setup>
import { ref, onMounted, watch, computed } from "vue";
import axios from "axios";
import navbar from "./nav.vue";
// import TarjetaProducto from "@/components/TarjetaProducto.vue";
import MostrarProductos from './MostrarProductosMain.vue';

const productos = ref([]);
const radioActual = ref(Number(localStorage.getItem('distancia_guardada')) || 10);

const categoriaSeleccionada = ref("Todas");

const manejarCambioRadio = (nuevoRadio) => {
    radioActual.value = nuevoRadio;
    mostrarProductos();
};

const categorias = computed(() => {
  const nombres = productos.value.map(p => p.categoria ? p.categoria.nombre_categoria : null);
  const unicas = [...new Set(nombres)].filter(Boolean);
  return ["Todas", ...unicas.sort()];
});

const mostrarProductos = async () => {
    const response = await axios.get("http://localhost:8080/api/productos", {
      params: {
        km: radioActual.value
      }
    });
    productos.value = response.data;
    console.log(response);
};

const productosFiltrados = computed(() => {
  if (categoriaSeleccionada.value === "Todas") return productos.value;
  
  return productos.value.filter(p => 
    p.categoria && p.categoria.nombre_categoria === categoriaSeleccionada.value
  );
});

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

      <div class="caja-filtro-especial">
        <button class="boton-secundario">
          <img src="../assets/iconos/filtro.png" alt="filtro" class="icono-pequeno"/>
          {{ categoriaSeleccionada === 'Todas' ? 'Filtros' : categoriaSeleccionada }}
        </button>

        <select v-model="categoriaSeleccionada" class="selector-invisible">
          <option v-for="cat in categorias" :key="cat" :value="cat">
            {{ cat === 'Todas' ? 'Todas las categorías' : cat }}
          </option>
        </select>
      </div> </div> <p class="informacion-resultados"> 
      {{ productosFiltrados.length }} productos encontrados <span class="texto-verde">(en un radio de {{ radioActual }} km)</span>
    </p>
  </div> <MostrarProductos :productos="productosFiltrados"></MostrarProductos>
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

.contenedor-filtro {
  position: relative;
}

.select-oculto {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  opacity: 0;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
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

.caja-filtro-especial {
  position: relative;
  display: flex;
}

.selector-invisible {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0; /* No se ve, pero se puede clicar */
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
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