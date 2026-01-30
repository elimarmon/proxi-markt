<script setup>
import SolicitarCompra from './SolicitarCompra.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Navbar from "@/components/nav.vue";

const props = defineProps(['id']);
const producto = ref(null);

const obtenerProducto = async () => {
    try {
        const response = await axios.get(`http://localhost:8080/api/productos/${props.id}`);
        producto.value = response.data;
        console.log("Producto cargado:", producto.value);
    } catch (error) {
        console.error("Error al obtener producto:", error);
    }
}
// esta funcio dona formato a la fecha per a que no es vega raro
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString();
}

const token = localStorage.getItem('token');

const crearCompraventa = (datosCompra) => {
    const payload = {
        id_producto: producto.value.id,
        id_vendedor: producto.value.id_usuario,
        id_punto: producto.value.id_puntoentrega,
        cantidad: datosCompra.cantidad,
        precio: producto.value.precio,
        fecha_prevista: datosCompra.fecha,
    }
    axios.post(`http://localhost:8080/api/compraventa/${props.id}`, payload, { 
        headers: { 
            'Authorization': `Bearer ${token}`
        } 
    });
}

onMounted(() => obtenerProducto());
</script>

<template>
  <navbar />
  <div class="contenedor-pagina" v-if="producto">
    <h1 class="titulo-verde">{{ producto.nombre_producto }}</h1>
    <p class="subtitulo">Detalles del producto y contacto con el productor</p>

    <div class="product-detail-card">
      <div class="image-section">
        <img :src="`http://localhost:8080/storage/${producto.imagen}`" :alt="producto.nombre_producto" />
      </div>

      <div class="info-section">
        <div class="header-info">
          <div class="title-row">
            <span class="badge" v-if="producto.categoria">{{ producto.categoria.nombre_categoria }}</span>
            <p class="price">{{ producto.precio }}€</p>
          </div>
        </div>

        <div class="details-box">
          <div class="detail-item">
            <span class="label">Stock disponible:</span>
            <span class="valor-verde">{{ producto.stock_total }} unidades</span>
          </div>
          
          <div class="detail-item" v-if="producto.punto_entrega">
            <span class="label">Punto de entrega:</span>
            <span>{{ producto.punto_entrega.nombre_punto }}</span>
            <small class="direccion">{{ producto.punto_entrega.direccion_punto }}</small>
          </div>

          <div class="detail-item" v-if="producto.usuario">
            <span class="label">Vendedor:</span>
            <span>{{ producto.usuario.nombre_usuario }}</span>
          </div>
        </div>

        <div class="form-container">
          <SolicitarCompra :precio="producto.precio" @enviar-solicitud="crearCompraventa" />
        </div>
      </div>
    </div>

    <div class="description-section">
      <h3>Descripción</h3>
      <p>{{ producto.descripcion }}</p>
    </div>
  </div>
</template>

<style scoped>
.contenedor-pagina {
  max-width: 1200px;
  margin: 0 auto;
  padding: 120px 20px 40px; 
  font-family: 'Segoe UI', 'Arial';
}

.titulo-verde {
  color: #4CA626;
  font-size: 2.2rem;
  margin-bottom: 5px;
  font-weight: bold;
}

.subtitulo {
  color: #666666;
  margin-bottom: 30px;
}

.product-detail-card {
  display: flex;
  flex-direction: row; 
  gap: 40px;
  background: transparent; 
  margin-bottom: 40px;
}

.image-section {
  flex: 1.2;
  min-width: 0; 
}

.image-section img {
  width: 100%;
  height: auto;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
  object-fit: cover;
}

.info-section {
  flex: 1;
}

.price {
  color: #333;
  font-size: 2.5rem;
  font-weight: bold;
}

.details-box, 
.form-container, 
.description-section {
  background: #FFFFFF;
  border: 1px solid #EEEEEE;
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); 
}

.detail-item {
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
}

.label {
  font-size: 0.75rem;
  color: #999;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
}

.boton-primario {
  background: linear-gradient(to right, #5cb82a, #008f4c);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 12px 24px;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.boton-primario:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
}

.boton-primario:active {
  transform: translateY(0);
}

.description-section h3 {
  color: #333;
  font-size: 1.2rem;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 1px solid #f0f0f0;
}

.description-section p {
  color: #555;
  line-height: 1.6;
}

@media (max-width: 992px) {
  .product-detail-card { gap: 25px; }
  .titulo-verde { font-size: 1.8rem; }
}

@media (max-width: 768px) {
  .contenedor-pagina { padding-top: 100px; }
  .product-detail-card { flex-direction: column; gap: 20px; }
  .image-section img { max-height: 400px; }
  .titulo-verde { text-align: center; }
  .subtitulo { text-align: center; }
}
</style>