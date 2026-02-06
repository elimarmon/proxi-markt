<script setup>
import SolicitarCompra from './SolicitarCompra.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import NavBar from "@/components/NavBar.vue";

const props = defineProps(['id']);
const producto = ref(null);

const obtenerProducto = async () => {
    const response = await axios.get(`http://localhost:8080/api/productos/${props.id}`);
    producto.value = response.data;
    console.log(producto.value)
}

const token = localStorage.getItem('token');

const crearCompraventa = (datosCompra) => {
    const payload = {
        id_producto: producto.value.id,
        id_vendedor: producto.value.id_usuario,
        id_punto: producto.value.id_puntoentrega,
        cantidad: datosCompra.cantidad,
        precio: producto.value.precio,
        fecha_prevista: datosCompra.fecha_prevista,
    }

    try {
        if(datosCompra.mensaje){
            const datoschat = {
                id_vendedor: producto.value.id_usuario,
                id_producto: producto.value.id,
                contenido: datosCompra.mensaje
                
            }
            axios.post('http://localhost:8080/api/enviarmensaje', datoschat, {
                headers: { 'Authorization': `Bearer ${token}`
                }
            })
        }
        axios.post(`http://localhost:8080/api/compraventa/${props.id}`, payload, { 
            headers: { 'Authorization': `Bearer ${token}` 
            } 
        })
        alert("Solicitud correcta");
    } catch (err) {
        alert("Solicitud incorrecta");
        console.log(err);
    }
}

onMounted(() => obtenerProducto());
</script>

<template>
    <NavBar />
    <div class="contenedor-pagina" v-if="producto">
        <div class="header-compacto">
            <h1 class="titulo-verde">{{ producto.nombre_producto }}</h1>
            <p class="subtitulo">Detalles del producto y contacto con el productor</p>
        </div>

        <div class="product-detail-card">
            <!-- Sección Izquierda: Imagen + Descripción -->
            <div class="image-section">
                <div class="image-wrapper">
                    <!-- Categoría encima de la imagen -->
                    <span class="badge-overlay" v-if="producto.categoria">
                        {{ producto.categoria.nombre_categoria }}
                    </span>
                    <img :src="`http://localhost:8080/storage/${producto.imagen}`" :alt="producto.nombre_producto" />
                </div>

                <div class="description-section">
                    <h3>Descripción</h3>
                    <p>{{ producto.descripcion }}</p>
                </div>
            </div>

            <!-- Sección Derecha: Información y Formulario -->
            <div class="info-section">
                <div class="details-box">
                    <div class="detail-item">
                        <span class="label">Stock disponible:</span>
                        <span class="valor-verde">{{ producto.stock_real }} unidades</span>
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

                    <!-- Precio en la parte inferior del cuadro -->
                    <div class="price-section">
                        <span class="label">Precio:</span>
                        <p class="price">{{ producto.precio }}€</p>
                    </div>
                </div>

                <div class="form-container">
                    <SolicitarCompra :precio="producto.precio" @enviar-solicitud="crearCompraventa" />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.contenedor-pagina {
    max-width: 1200px;
    margin: 0 auto;
    /* Reducido para que quepa mejor en el viewport */
    padding: 90px 20px 20px;
    font-family: 'Segoe UI', 'Arial', sans-serif;
    min-height: 100vh;
}

.header-compacto {
    margin-bottom: 20px;
}

.titulo-verde {
    color: #4CA626;
    font-size: 2rem;
    margin-bottom: 2px;
    font-weight: bold;
}

.subtitulo {
    color: #666666;
    margin-bottom: 0;
    font-size: 0.95rem;
}

.product-detail-card {
    display: flex;
    flex-direction: row;
    gap: 30px;
    background: transparent;
    align-items: flex-start;
}

.image-section {
    flex: 1.3;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.image-wrapper {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.image-wrapper img {
    width: 100%;
    /* Altura máxima para no empujar la descripción fuera del viewport */
    max-height: 50vh;
    height: auto;
    object-fit: cover;
    display: block;
}

.badge-overlay {
    position: absolute;
    top: 12px;
    right: 12px;
    background-color: rgba(76, 166, 38, 0.9);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    z-index: 10;
    text-transform: uppercase;
}

.info-section {
    flex: 1;
    position: sticky;
    top: 90px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.details-box,
.form-container,
.description-section {
    background: #FFFFFF;
    border: 1px solid #EEEEEE;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.price-section {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.price {
    color: #333;
    font-size: 2.2rem;
    font-weight: bold;
    margin: 0;
}

.description-section {
    margin-top: 0;
}

.detail-item {
    margin-bottom: 12px;
    display: flex;
    flex-direction: column;
}

.detail-item:last-of-type {
    margin-bottom: 0;
}

.label {
    font-size: 0.7rem;
    color: #999;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
}

.valor-verde {
    color: #4CA626;
    font-weight: bold;
}

.direccion {
    color: #888;
    font-size: 0.8rem;
    margin-top: 1px;
}

.description-section h3 {
    color: #333;
    font-size: 1.1rem;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f0f0f0;
}

.description-section p {
    color: #555;
    line-height: 1.5;
    font-size: 0.95rem;
    margin: 0;
}

@media (max-width: 992px) {
    .product-detail-card {
        gap: 20px;
    }
    .titulo-verde { font-size: 1.6rem; }
}

@media (max-width: 768px) {
    .contenedor-pagina {
        padding-top: 80px;
    }

    .product-detail-card {
        flex-direction: column;
    }

    .image-wrapper img {
        max-height: 350px;
    }

    .info-section {
        position: static;
    }
}
</style>