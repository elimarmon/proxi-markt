<script setup>
import { ref, onMounted, computed } from "vue";
import api from "@/api/axios";
import NavBar from "./NavBar.vue";
import ValoracionForm from "./ValoracionForm.vue";
import { useAuth } from "@/composables/useAuth";
import { storageUrl, storageDefaultProductUrl } from "@/utils/storage";

const comandas = ref([]);
const cargando = ref(true);
const { usuario, fetchUsuario } = useAuth();
const aValorar = ref(null);

// --- VARIABLES PARA EL TOAST ---
const toastVisible = ref(false);
const toastMensaje = ref("");

// --- FUNCIÓN PARA ACTIVAR EL TOAST ---
const lanzarToast = (mensaje) => {
    toastMensaje.value = mensaje;
    toastVisible.value = true;
    setTimeout(() => {
        toastVisible.value = false;
    }, 3000); // Se oculta a los 3 segundos
};

const obtenerComandas = async () => {
    try {
        const response = await api.get("/mis-comandas");
        comandas.value = response.data.datos;
    } catch (error) {
        console.error("Error al cargar:", error);
    } finally {
        cargando.value = false;
    }
};

const comandasPendientes = computed(() => {
    return comandas.value.filter(c => c.estado == 'pendiente' || c.estado == 'en curso');
});

const historialComandas = computed(() => {
    return comandas.value.filter(c => c.estado == 'completado' || c.estado == 'cancelado' || c.estado == 'valorado');
});

const actualizarComanda = async (id, nuevoEstado) => {
    try {
        await api.put(`/mis-comandas/${id}`, { estado: nuevoEstado });
        const comandaEncontrada = comandas.value.find(c => c.id === id);
        if (comandaEncontrada) comandaEncontrada.estado = nuevoEstado;
    } catch (err) {
        lanzarToast("Ha ocurrido un error al actualizar la comanda.");
        console.error(err);
    }
}

const abrirModalValoracion = (id) => {
    aValorar.value = id;
};

const getColoresEstado = (estado) => {
    const paleta = {
        'pendiente': '#ff7519',
        'en curso': '#3498db',
        'completado': '#4CA626',
        'cancelado': '#e74c3c',
        'valorado': '#4CA626',
    };
    return paleta[estado] || '#64748b';
};

const formatearFecha = (fecha) => {
    if (!fecha) return '';
    const [year, month, day] = fecha.split('-'); 
    return `${day}/${month}/${year}`;
};

const getNombreContraparte = (comanda) => {
    if (comanda.id_comprador === usuario.value.id) {
        return comanda.vendedor?.nombre_usuario || 'Vendedor';
    }
    return comanda.comprador?.nombre_usuario || 'Comprador';
};

onMounted(async () => {
    await fetchUsuario();
    if (usuario.value?.id) obtenerComandas();
});

const getUrlImagen = (rutaRelativa) => {
    return rutaRelativa ? storageUrl(rutaRelativa) : storageDefaultProductUrl();
};

const postValoracion = async (idCompraventa, datos) => {
    try {
        await api.post(`/valoraciones/${idCompraventa}`, datos);
        alert("Valoración realizada.")
        aValorar.value = null;
        const comanda = comandas.value.find(c => c.id === idCompraventa);
        if (comanda) {
            comanda.estado = 'valorado';
        }
    } catch (err) {
        lanzarToast("Algo ha ido mal.");
        console.log(err);
    }
};
</script>

<template>
    <NavBar />
    <div class="contenedor-pagina">
        <div id="contenedor-titulo">
            <h1 class="titulo">Comandas</h1>
            <p class="subtitulo">Gestiona las solicitudes de compra de tus productos</p>
        </div>

        <div class="seccion-comandas">
            <div class="cabecera-seccion">
                <div class="titulo-grupo">
                    <img src="../assets/iconos/stock.png" alt="Icono" class="icono-seccion" />
                    <h3>Comandas en curso</h3>
                </div>
                <span class="contador-badge">{{ comandasPendientes.length }} pendientes</span>
            </div>

            <p v-if="cargando" class="texto-info">Cargando comandas...</p>
            <div v-if="!cargando && comandasPendientes.length === 0" class="sin-datos">
                No hay comandas pendientes
            </div>

            <div v-for="comanda in comandasPendientes" :key="comanda.id" class="tarjeta-comanda"
                :style="{ borderLeftColor: getColoresEstado(comanda.estado) }">
                <div class="info-principal">
                    <img :src="getUrlImagen(comanda.producto?.imagen)" class="img-producto" />
                    <div class="detalles">
                        <h3>{{ comanda.producto?.nombre_producto || "Producto" }}</h3>
                        <div class="fila-datos">
                            <span>Cant: {{ comanda.cantidad }}</span>
                            <span class="separador">•</span>
                            <span class="precio">{{ comanda.precio_total }}€</span>
                            <span class="separador">•</span>
                            <span class="usuario">{{ getNombreContraparte(comanda) }}</span>
                            <span class="separador">•</span>
                            <span class="fecha">{{ formatearFecha(comanda.fecha_prevista) }}</span>
                        </div>
                    </div>
                </div>

                <div class="acciones">
                    <button v-if="comanda.estado == 'en curso' && comanda.id_comprador !== usuario.id"
                        class="btn-accion finalizar" @click="actualizarComanda(comanda.id, 'completado')">
                        Finalizar
                    </button>
                    <button v-else-if="comanda.id_comprador !== usuario.id" class="btn-accion aceptar"
                        @click="actualizarComanda(comanda.id, 'en curso')">
                        Aceptar
                    </button>
                    <button class="btn-accion rechazar" @click="actualizarComanda(comanda.id, 'cancelado')">
                        Rechazar
                    </button>
                </div>

                <div class="etiqueta-estado" :style="{ backgroundColor: getColoresEstado(comanda.estado) }">
                    {{ comanda.estado }}
                </div>
            </div>
        </div>

        <div class="seccion-comandas">
            <div class="cabecera-seccion">
                <div class="titulo-grupo">
                    <img src="../assets/iconos/aceptar.png" alt="Historial" class="icono-seccion">
                    <h3>Historial de comandas</h3>
                </div>
            </div>

            <div v-if="historialComandas.length === 0" class="sin-datos">
                No hay historial disponible.
            </div>

            <div v-for="item in historialComandas" :key="item.id" class="tarjeta-comanda"
                :style="{ borderLeftColor: getColoresEstado(item.estado) }">

                <div class="info-principal">
                    <img :src="getUrlImagen(item.producto?.imagen)" class="img-producto">
                    <div class="detalles">
                        <h3>{{ item.producto?.nombre_producto }}</h3>
                        <div class="fila-datos">
                            <span>Cant: {{ item.cantidad }}</span>
                            <span class="separador">•</span>
                            <span class="precio">{{ item.precio_total }}€</span>
                            <span class="separador">•</span>
                            <span class="usuario">{{ getNombreContraparte(item) }}</span>
                            <span class="separador">•</span>
                            <span class="fecha">{{ formatearFecha(item.fecha_prevista) }}</span>
                        </div>
                    </div>
                </div>

                <!-- No es pot dependre d'un estat global. He de registrar qui ha valorat i qui no -->
                <div class="acciones">
                    <button v-if="item.estado == 'completado' || item.estado == 'valorado'"
                        :disabled="item.estado == 'valorado'" class="btn-accion valorar"
                        @click="abrirModalValoracion(item.id)">

                        <span v-if="item.estado == 'completado'">Valorar</span>

                        <span v-else class="d-flex align-items-center gap-1">
                            <i class="bi bi-check-circle-fill"></i> Ya valorado
                        </span>
                    </button>
                </div>

                <div class="etiqueta-estado" :style="{ backgroundColor: getColoresEstado(item.estado) }">
                    {{ item.estado }}
                </div>
            </div>
        </div>

        <ValoracionForm v-if="aValorar" :id="aValorar" @enviar-valoracion="postValoracion(aValorar, $event)"
            @cerrar="aValorar = null" />
    </div>
</template>

<style scoped>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", "Arial", sans-serif;
}

.contenedor-pagina {
    margin-top: 80px;
    padding: 20px 5%;
}

#contenedor-titulo {
    margin-bottom: 40px;
}

.titulo {
    color: #4ca626;
    font-weight: bold;
    font-size: 2rem;
}

.subtitulo {
    color: #666;
}

.seccion-comandas {
    margin-bottom: 50px;
}

.cabecera-seccion {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.titulo-grupo {
    display: flex;
    align-items: center;
    gap: 12px;
}

.icono-seccion {
    width: 30px;
    height: 30px;
}

.contador-badge {
    background-color: #B9E2A6;
    color: #4CA626;
    padding: 6px 15px;
    border-radius: 8px;
    font-weight: bold;
}

.tarjeta-comanda {
    background: white;
    border: 1px solid #f0f0f0;
    border-left: 6px solid #ccc;
    border-radius: 12px;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
    position: relative;
}

.info-principal {
    display: flex;
    align-items: center;
    gap: 15px;
}

.img-producto {
    width: 70px;
    height: 70px;
    border-radius: 10px;
    object-fit: cover;
}

.detalles h3 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    color: #1e293b;
    font-weight: 700;
}

.fila-datos {
    display: flex;
    gap: 8px;
    font-size: 0.9rem;
    color: #64748b;
    align-items: center;
    flex-wrap: wrap;
}

.precio {
    color: #4CA626;
    font-weight: 700;
}

.separador {
    color: #cbd5e1;
    font-size: 10px;
}

.acciones {
    display: flex;
    gap: 10px;
    margin-right: 120px;
}

.btn-accion {
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: 0.2s;
}

.btn-accion:disabled {
    background-color: #f1f5f9;
    color: #94a3b8;
    cursor: not-allowed;
    border: 1px solid #e2e8f0;
    transform: none;
    box-shadow: none;
}

.valorar:disabled {
    opacity: 0.8;
    font-style: italic;
}

.d-flex {
    display: flex;
}

.align-items-center {
    align-items: center;
}

.gap-1 {
    gap: 4px;
}

.aceptar,
.finalizar {
    background: #4CA626;
    color: white;
}

.rechazar {
    background: #fee2e2;
    color: #e74c3c;
    border: 1px solid #e74c3c;
}

.valorar {
    background: #3498db;
    color: white;
}

.etiqueta-estado {
    position: absolute;
    right: 20px;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: bold;
    text-transform: uppercase;
}

.sin-datos {
    text-align: center;
    padding: 40px;
    background: #fcfcfc;
    border-radius: 12px;
    color: #999;
    border: 1px dashed #ddd;
}

.texto-info {
    text-align: center;
    padding: 20px;
    color: #666;
}

@media (max-width: 900px) {
    .tarjeta-comanda {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .acciones {
        margin-right: 0;
        width: 100%;
    }

    .etiqueta-estado {
        top: 15px;
    }
}
</style>
