<script setup>
const props = defineProps({
  productos: {
    type: Array,
    required: true,
    default: () => []
  }
});
</script>

<template>
  <div class="contenedor-seccion-productos">
    <div v-if="productos && productos.length > 0" class="grid-productos">
      <div v-for="producto in productos" :key="producto.id" class="card-producto">
        <div class="imagen-contenedor">
          <img 
            :src="producto.imagen ? `http://localhost:8080/storage/${producto.imagen}` : 'https://via.placeholder.com/150'" 
            alt="Imagen producto"
          >
          <span class="badge-estado" :class="producto.estado">{{ producto.estado }}</span>
        </div>
        
        <div class="detalles-producto">
          <h4>{{ producto.nombre_producto }}</h4>
          <p class="precio">{{ producto.precio }}€</p>
          <div class="meta-info">
            <span><img src="../assets/iconos/stock.png" alt="caja-icono" class="icono"> Stock: {{ producto.stock_total }}</span>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="card-vacia">
      No hay productos para mostrar en este momento.
    </div>
  </div>
</template>


<style scoped>
.meta-info span {
  display: flex;
  align-items: center;
  gap: 5px;
}

.icono {
    width: 25px;
    height: 25px;
}

.grid-productos {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 20px;
  width: 100%;
}

.card-producto {
  background: white;
  border: 1px solid #eee;
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.2s;
}
.card-producto:hover {
  transform: translateY(-5px);
}

.imagen-contenedor {
  position: relative; height: 140px;
}

.imagen-contenedor img {
  width: 100%; height: 100%; object-fit: cover;
}

.badge-estado {
  position: absolute; top: 10px; right: 10px; padding: 4px 8px;
  border-radius: 6px; font-size: 0.7rem; font-weight: bold;
}

.badge-estado.disponible {
  background: #dcfce7; color: #166534;
}

.detalles-producto {
  padding: 15px;
}

.precio {
  font-size: 1.2rem; font-weight: bold; color: #4CA626;
}

.card-vacia {
  background: white; border: 1px solid #eee; border-radius: 12px;
  padding: 40px; text-align: center; color: #999;
}
</style>