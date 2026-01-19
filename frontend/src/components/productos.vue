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

    <router-link v-for="producto in productos" :to="{name: 'detalles-producto', params: {id: producto.id}}" ><MostrarProductos :productos="productos"></MostrarProductos>
    </router-link>
  </div>
</template>

<style scoped>
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
    color: #4ca626;
    margin-bottom: 10px;
    font-weight: bold;
}

.subtitulo {
    font-family: sans-serif;
    color: #666666;
    margin-bottom: 20px;
}

#contenedor-buscador {
    background-color: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow:
        0 4px 10px rgba(0, 0, 0, 0.1),
        0 -4px 10px rgba(0, 0, 0, 0.1);
    font-family: sans-serif;
    max-width: 90%;
    margin: 40px auto 0 auto;
}

#buscador {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.caja {
    background-color: #f3f4f6;
    border-radius: 8px;
    padding: 15px 15px;
    display: flex;
    align-items: center;
    flex-grow: 1;
    border: 1px solid #e5e7eb;
}

.texto {
    border: none;
    background: transparent;
    width: 100%;
    margin-left: 10px;
    outline: none;
    font-size: 16px;
    color: #333333;
}

.caja:focus-within {
    background-color: #f3f4f6;
    border-color: #d1d5db;
    box-shadow: 0 0 0 3px rgba(0, 176, 80, 0.1);
}

.boton-filtro {
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 15px 20px;
    height: 100%;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: bold;
    font-size: 18px;
    color: #333333;
    white-space: nowrap;
}

.icono {
    width: 30px;
    height: 30px;
}

.informacion-filtro {
    color: #666666;
    align-items: center;
}

.texto-verde {
    color: #4ca626;
}

.seccion-productos {
    /* Eliminamos el max-width de 1200px y usamos el mismo del buscador */
    max-width: 90%;
    margin: 40px auto; /* Mismo margen que #contenedor-titulo y #contenedor-buscador */
    padding: 0; /* Quitamos el padding para que el borde sea exacto */
}

.seccion-bloque h3 {
  font-size: 1.1rem;
  margin-bottom: 15px;
  color: #333;
  padding-left: 5px;
  font-weight: bold;
}

/* Grid de productos */
.contenedor-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 25px;
}

/* Card vacía igual que en Mi Cuenta */
.card-vacia {
  background: white;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 60px;
  text-align: center;
  color: #999;
  width: 100%;
}

/* Responsivo */
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
