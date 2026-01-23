<template>
  <navbar></navbar>
  <div class="contenedor-pagina">
    <div id="contenedor-titulo">
      <h1 class="titulo">Dashboard</h1>
      <p class="subtitulo">Resumen de tu actividad en ProxiMarkt</p>
    </div>

    <div class="cajas-informacion-uno">
      <div class="caja">
        <h3>Mis productos</h3>
        <p>{{ ProductosUser.length }}</p>
        <img
          src="../assets/iconos/brote.png"
          alt="Mis productos"
          class="icono"
        />
      </div>

      <div class="caja">
        <h3>Stock total</h3>
        <p>55</p>
        <img src="../assets/iconos/ingresos.png" alt="Stock total" />
      </div>

      <div class="caja">
        <h3>Ventas pendientes</h3>
        <p>3</p>
        <img src="../assets/iconos/info.png" alt="Ventas pendientes" />
      </div>

      <div class="caja">
        <h3>Ingresos</h3>
        <p>5.40€</p>
        <img src="../assets/iconos/euro.png" alt="Ingresos" />
      </div>
    </div>
    <div class="cajas-informacion-dos">
      <div class="ventas">
        <img
          src="../assets/iconos/carrito.png"
          alt="Ventas recientes"
          class="icono"
        />
        <h3>Ventas Recientes</h3>
        <div class="producto-ventas">
          <p id="nombre-producto">Lechuga Fresca</p>
          <p id="precio">5.40€</p>
          <p id="info">Ana</p>
          <p id="estado">Completada</p>
        </div>
      </div>

      <div class="productos">
        <img
          src="../assets/iconos/disponibles.png"
          alt="Productos disponibles"
          class="icono"
        />
        <h3>Productos disponibles</h3>
        <div v-if="ProductosUser.length > 0">
          <div class="producto-disponible" v-for="producto in ProductosUser" :key="producto.id">
            <img :src="producto.imagen ? `http://localhost:8080/storage/${producto.imagen}` : 'https://via.placeholder.com/150'"
            alt="Imagen producto" class="imagen-producto">
            <p id="nombre-producto">{{ producto.nombre_producto }}</p>
            <p id="precio-producto">{{ producto.precio }}€</p>
            <p id="stock-disponible">{{ producto.stock_total }} disponibles</p>
          </div>
        </div>
        
      </div>
    </div>

    <div class="cajas-informacion-tres">
      <div class="productos">
        <img
          src="../assets/iconos/stock.png"
          alt="Mis compras recientes"
          class="icono"
        />
        <h3>Mis Compras Recientes</h3>
        <div class="compras-producto">
          <p id="nombre-producto">Lechuga Fresca</p>
          <p id="info">Vendedor: Juan</p>
          <p id="estado">Pendiente</p>
          <p id="precio">5.00€</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import navbar from "./nav.vue";

const router = useRouter();

const ProductosUser = ref([]);
// const datosUsuario = ref ({});

const CargarProductosUser = async () => {
  const token = localStorage.getItem('token');

  const productos = await axios.get('http://localhost:8080/api/productosuser', {
    headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }}
  );

  ProductosUser.value = productos.data;
  console.log(productos.data);
}

onMounted(() => {
      CargarProductosUser();
  });
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Segoe UI", "Arial";
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
  color: #4CA626;
  margin-bottom: 10px;
  font-weight: bold;
}

.subtitulo {
  font-family: sans-serif;
  color: #666666;
  margin-bottom: 20px;
}

.cajas-informacion-uno,
.cajas-informacion-dos,
.cajas-informacion-tres {
  max-width: 90%;
  margin-left: auto;
  margin-right: auto;
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}

.cajas-informacion-uno {
  display: flex;
  gap: 20px;
  margin-bottom: 30px;
}

.caja {
  flex: 1;
  min-width: 200px;
  border-radius: 12px;
  padding: 20px;
  color: white;
  display: flex;
  flex-direction: column;
  justify-content: center;
  position: relative;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.caja h3 {
  font-size: 20px;
  opacity: 0.9;
  z-index: 2;
}

.caja p {
  font-size: 28px;
  font-weight: bold;
  margin-top: 5px;
  z-index: 2;
}

.caja img {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  filter: brightness(0) invert(1);
  width: 70px;
  padding: 10px;
  background-color: rgba(255, 255, 255, 0.25);
  border-radius: 10px;
}

.cajas-informacion-uno .caja:nth-child(1) {
  background: linear-gradient(to left, #009E5B, #20c97e);
}

.cajas-informacion-uno .caja:nth-child(2) {
  background: linear-gradient(to left, #1060C4, #4facfe);
}

.cajas-informacion-uno .caja:nth-child(3) {
  background: linear-gradient(to left, #D95000, #ff8c42);
}

.cajas-informacion-uno .caja:nth-child(4) {
  background: linear-gradient(to left, #801AC0, #b845fc);
}

.cajas-informacion-dos,
.cajas-informacion-tres {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}

.ventas,
.productos {
  background: white;
  border-radius: 12px;
  padding: 20px;
  flex: 1;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  border: 1px solid #EEEEEE;
}

.ventas h3,
.productos h3 {
  display: inline-block;
  vertical-align: middle;
  margin: 0;
  margin-bottom: 20px;
  font-size: 18px;
  color: #333333;
}

.ventas .icono,
.productos .icono {
  display: inline-block;
  vertical-align: middle;
  width: 30px;
  margin-right: 10px;
  margin-bottom: 20px;
}

.producto-disponible, 
.compras-producto,
.producto-ventas {
  display: grid;
  grid-template-columns: auto 1fr auto;
  grid-template-rows: auto auto;
  gap: 0px 15px;
  padding: 15px;
  background-color: #F9FAFB;
  border-radius: 12px;
  margin-bottom: 10px;
  align-items: center;
}

.producto-disponible .imagen-producto,
.compras-producto .imagen-producto,
.producto-ventas .imagen-producto {
  grid-column: 1;
  grid-row: 1 / 3;
  width: 50px;
  height: 50px;
  border-radius: 8px;
  object-fit: cover;
}

.producto-disponible #nombre-producto,
.compras-producto #nombre-producto,
.producto-ventas #nombre-producto {
  grid-column: 2;
  grid-row: 1;
  font-weight: bold;
  color: #333333;
  font-size: 15px;
  margin-bottom: 2px;
  align-self: end;
}

.producto-disponible #info,
.compras-producto #info,
.producto-ventas #info {
  grid-column: 2;
  grid-row: 2;
  font-size: 13px;
  color: #64748B;
  align-self: start;
}

.producto-disponible #precio-producto,
.compras-producto #precio,
.producto-ventas #precio {
  grid-column: 3;
  grid-row: 1;
  text-align: right;
  font-weight: bold;
  color: #333333;
  font-size: 15px;
  align-self: end;
}

.producto-disponible #stock-disponible,
.compras-producto #estado,
.producto-ventas #estado {
  grid-column: 3;
  grid-row: 2;
  text-align: right;
  font-size: 12px;
  align-self: start;
}

.producto-disponible #stock-disponible {
    color: #64748B;
}

.compras-producto #estado {
    background: #FFF4E6;
    color: #FF9F43;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: bold;
    margin-top: 2px;
}

.producto-ventas #estado {
    background: #E0F8E9;
    color: #00B86B;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: bold;
    margin-top: 2px;
}
</style>