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
      <div v-for="producto in productos" :key="producto.id" class="carta-producto">
        <router-link :to="{name: 'detalle-productos', params: {id: producto.id}}" class="carta-link">

          <div class="imagen-contenedor">
            <img :src="producto.imagen ? `http://localhost:8080/storage/${producto.imagen}` : 'https://via.placeholder.com/400x300'" 
              alt="Imagen producto" class="imagen-producto"            >
            <span class="estado">{{ producto.estado || 'Verduras' }}</span>
          </div>

          <div class="detalles-producto">
            <h4 class="nombre">{{ producto.nombre_producto }}</h4>
            <p class="precio">{{ producto.precio }}€</p>
            
            <p class="descripcion">{{ producto.descripcion || 'Descripción del producto con variedades de temporada y sabor auténtico. Ideal para ensaladas.' }}</p>
            
            <div class="informacion">
              <div class="objeto">
                <img class="icono" src="" alt=""> <span class="texto-gris">Mercado de Santa Caterina</span>
              </div>

              <div class="meta-item">
                <img class="icono" src="" alt=""> <span class="texto-azul">A 7.8 km de ti</span>
              </div>
              
              <div class="meta-item">
                <img class="icono" src="" alt="">
                <span class="texto-verde">{{ producto.stock_total }} disponibles</span>
              </div>
            </div>

            <div class="boton-verde">Ver detalles</div>
          </div>
        </router-link>
      </div>
    </div>

    <div v-else class="card-vacia">No hay productos encontrados en este radio.</div>
  </div>
</template>

<style scoped>
.contenedor-seccion-productos {
  font-family: 'Segoe UI', Arial;
  padding: 20px 0;
  background-color: transparent;
}

.grid-productos {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 30px;
  width: 100%;
  margin: 0 auto;
}

.carta-producto {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s, box-shadow 0.2s;
  display: flex;
  flex-direction: column;
}

.carta-producto:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.carta-link {
  text-decoration: none;
  color: inherit;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.imagen-contenedor {
  position: relative;
  height: 240px; 
  width: 100%;
  background-color: #f3f4f6;
}

.imagen-producto {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.estado {
  position: absolute;
  top: 12px;
  right: 12px;
  background-color: #10b981;
  color: white;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 700;
}

.detalles-producto {
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.nombre {
  margin: 0 0 5px 0;
  font-size: 1.15rem;
  font-weight: 600;
  color: #111827;
}

.precio {
  font-size: 1.5rem;
  font-weight: 600;
  color: #10b981;
  margin: 0 0 10px 0;
}

.descripcion {
  font-size: 0.9rem;
  color: #6b7280;
  line-height: 1.5;
  margin-bottom: 20px;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.informacion {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}

.objeto {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.85rem;
}

.icono {
  width: 16px;
  height: 16px;
  display: block;
  background-color: transparent; 
}

.texto-gris { color: #6b7280; }
.texto-azul { color: #2563eb; }
.texto-verde { color: #059669; font-weight: 500; }

.boton-verde {
  margin-top: auto;
  background-color: #00b050;
  color: white;
  text-align: center;
  padding: 10px 0;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.95rem;
  transition: background-color 0.2s;
}

.carta-producto:hover .boton-verde {
  background-color: #009142;
}

.carta-vacia {
  text-align: center;
  padding: 50px;
  color: #9ca3af;
  background: white;
  border: 1px dashed #e5e7eb;
  border-radius: 12px;
}
</style>