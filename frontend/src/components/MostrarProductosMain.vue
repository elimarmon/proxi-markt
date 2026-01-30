<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
  productos: {
    type: Array,
    default: () => []
  },
  radioMaximo: {
    type: Number,
    default: 10
  }
});


const miUsuario = ref(null);
const productoubicacion = ref(null);

const productosFiltrados = computed(() => {
  if(!props.productos) return [];
  return props.productos.filter(producto => {
    const distancia = calcularKm(
      producto.punto_entrega?.latitud,
      producto.punto_entrega?.longitud
    );

    if(distancia === '--') return false;
    return parseFloat(distancia) <= props.radioMaximo;
  });
});

const obtenerMiUbicacion = async () => {
  const token = localStorage.getItem('token');
  if (!token) return;

  try {
    const usuario = await axios.get('http://localhost:8080/api/datosuser', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const productosResp = await axios.get('http://localhost:8080/api/productos', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    miUsuario.value = usuario.data;
    productoubicacion.value = productosResp.data;

  } catch (error) {
    console.error("Error cargando datos:", error);
  }
}

onMounted(() => {
  obtenerMiUbicacion();
});

const calcularKm = (latVendedor, lngVendedor) => {

  if (!miUsuario.value || !miUsuario.value.latitud) {
    return '--';
  }

  if (!latVendedor || !lngVendedor) {
    return '--';
  }

  const miLat = parseFloat(miUsuario.value.latitud);
  const miLng = parseFloat(miUsuario.value.longitud);

  const vendLat = parseFloat(latVendedor);
  const vendLng = parseFloat(lngVendedor);

  const p = Math.PI / 180;
  const c = Math.cos;
  const a = 0.5 - c((vendLat - miLat) * p) / 2 +
    c(miLat * p) * c(vendLat * p) * (1 - c((vendLng - miLng) * p)) / 2;

  return (12742 * Math.asin(Math.sqrt(a))).toFixed(1);
}
</script>

<template>
    <div class="contenedor-seccion-productos">
        <div v-if="productosFiltrados && productosFiltrados.length > 0" class="grid-productos">
      <div v-for="producto in productosFiltrados" :key="producto.id" class="carta-producto">
                <router-link :to="{ name: 'detalle-productos', params: { id: producto.id } }" class="carta-link">

                    <div class="imagen-contenedor">
                        <img :src="producto.imagen ? `http://localhost:8080/storage/${producto.imagen}` : 'https://via.placeholder.com/400x300'"
                            alt="Imagen producto" class="imagen-producto">
                        <span class="categoria">{{ producto.categoria.nombre_categoria || 'Sin categoría' }}</span>
                    </div>

                    <div class="detalles-producto">
                        <h4 class="nombre">{{ producto.nombre_producto }}</h4>
                        <p class="precio">{{ producto.precio }}€</p>

                        <p class="descripcion">{{ producto.descripcion }}</p>

                        <div class="informacion">
                            <div class="objeto">
                                <img class="icono" src="../assets/iconos/casa.png" alt="granja">
                                <span class="texto-gris">{{ producto.punto_entrega?.nombre_punto || 'Sin ubicación' }}</span>
                            </div>

                            <div class="objeto">
                                <img class="icono" src="../assets/iconos/ubicacion.png" alt="direccion"> <span
                                    class="texto-azul">A {{ calcularKm(producto.punto_entrega?.latitud, producto.punto_entrega?.longitud) }} km de ti</span>
                            </div>

                            <div class="objeto">
                                <img class="icono" src="../assets/iconos/stock.png" alt="stock">
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
    font-family: 'Segoe UI', 'Arial';
    padding: 20px 0;
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
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
}

.carta-producto:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
}

.carta-link {
    text-decoration: none;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.imagen-contenedor {
    position: relative;
    height: 240px;
    width: 100%;
    background-color: #F3F4F6;
    overflow: hidden;
}

.imagen-producto {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.carta-producto:hover .imagen-producto {
    transform: scale(1.1);
}

.categoria {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(to right, #00cf64, #009b4b);
    color: white;
    padding: 8px 15px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: bold;
}

.detalles-producto {
    padding: 10px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.nombre {
    margin: 0 0 5px 0;
    font-size: 1.15rem;
    font-weight: bold;
    color: #111827;
}

.precio {
    font-size: 1.5rem;
    font-weight: bold;
    color: #4CA626;
    margin: 0 0 10px 0;
}

.descripcion {
    font-size: 0.9rem;
    color: #6B7280;
    line-height: 1.5;
    margin-bottom: 20px;
    overflow: hidden;
}

.informacion {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 25px;
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

.texto-gris {
    color: #6B7280;
}

.texto-azul {
    color: #2563EB;
}

.texto-verde {
    color: #4CA626;
}

.boton-verde {
    margin-top: auto;
    background: linear-gradient(90deg, #4CA626 0%, #009B58 100%);
    color: white;
    text-align: center;
    padding: 10px 0;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: background-color 0.2s;
}

.carta-producto:hover .boton-verde {
    background: linear-gradient(90deg, #008F4C 0%, rgb(1, 104, 59) 100%);
}

.carta-vacia {
    text-align: center;
    padding: 50px;
    color: #9CA3AF;
    background: white;
    border: 1px dashed #E5E7EB;
    border-radius: 12px;
}
</style>