<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import NavBar from "./NavBar.vue";
import ValoracionForm from "./ValoracionForm.vue";
import { useAuth } from "@/composables/useAuth";
import Footer from "./Footer.vue";

const comandas = ref([]);
const cargando = ref(true);
const { usuario, fetchUsuario } = useAuth();
const token = localStorage.getItem("token");
const aValorar = ref(null);

const obtenerComandas = async () => {
    try {
        const response = await axios.get("http://localhost:8080/api/mis-comandas", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        });
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
    return comandas.value.filter(c => c.estado == 'completado' || c.estado == 'cancelado');
});

const actualizarComanda = async (id, nuevoEstado) => {
    try {
        await axios.put(`http://localhost:8080/api/mis-comandas/${id}`,
            { estado: nuevoEstado },
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json"
                }
            });
        const comandaEncontrada = comandas.value.find(c => c.id === id);

        if (comandaEncontrada) {
            comandaEncontrada.estado = nuevoEstado;
        }
    } catch (err) {
        alert("Ha ocurrido un error al actualizar la comanda.");
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
        'cancelado': '#e74c3c'
    };
    return paleta[estado] || '#64748b';
};

onMounted(async () => {
    if (!usuario.value?.id) await fetchUsuario();
    obtenerComandas();
});

const getUrlImagen = (rutaRelativa) => {
    return rutaRelativa ? `http://localhost:8080/storage/${rutaRelativa}` : "http://localhost:8080/storage/productos/default.png";
};

const postValoracion = async (idCompraventa, datos) => {
    const token = localStorage.getItem('token');
    if (!token) return;
    try {
        await axios.post(`http://localhost:8080/api/valoraciones/${idCompraventa}`, datos, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
    } catch (err) {
        alert("Algo ha ido mal.");
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

            <div v-for="comanda in comandasPendientes" :key="comanda.id" class="comanda">
                <img :src="getUrlImagen(comanda.producto?.imagen)" alt="foto-producto" class="foto-producto" />

                <h3>
                    {{ comanda.producto?.nombre_producto || "Producto desconocido" }}
                </h3>

                <p id="estado">{{ comanda.estado }}</p>

                <div id="precio-total">
                    <p>{{ comanda.precio_total }}€</p>
                    <p>Total</p>
                </div>

                <div id="cantidad">
                    <img src="../assets/iconos/stock.png" alt="icono-cantidad" class="icono" />
                    <p>Cantidad: {{ comanda.cantidad }}</p>
                </div>

                <div id="horario">
                    <img src="../assets/iconos/calendario.png" alt="icono-calendario" class="icono" />
                    <p>{{ comanda.fecha_prevista }}</p>
                </div>

                <div id="usuario">
                    <img src="../assets/iconos/mi_cuenta_verde.png" alt="icono-cuenta" class="icono" />
                    <p>
                        {{ comanda.comprador?.nombre_usuario || "Usuario desconocido" }}
                    </p>
                </div>
                <!-- 
                <div class="mensaje-comprador">
                    <img src="../assets/iconos/chat-comanda.png" alt="icono-chat" class="icono" />
                    <p>Nota del pedido:</p>
                    <p>{{ comanda.mensaje || "No especificado" }}</p>
                </div> -->

                <button v-if="comanda.estado == 'completado'" class="aceptar"
                    @click="abrirModalValoracion(comanda.id)">Valorar</button>
                <button v-else-if="comanda.estado == 'en curso' && comanda['id_comprador'] !== usuario.id"
                    class="aceptar" @click="actualizarComanda(comanda.id, 'completado')">
                    <img src="../assets/iconos/aceptar.png" alt="icono-aceptar" class="icono" />
                    Finalizar comanda
                </button>
                <button v-else-if="comanda['id_comprador'] !== usuario.id"
                    @click="actualizarComanda(comanda.id, 'en curso')" class="aceptar">
                    <img src="../assets/iconos/aceptar.png" alt="icono-aceptar" class="icono" />
                    Aceptar comanda
                </button>

                <button :disabled="comanda.estado == 'completado'" class="rechazar"
                    @click="actualizarComanda(comanda.id, 'cancelado')">
                    <img src="../assets/iconos/rechazar.png" alt="icono-rechazar" class="icono" />
                    Rechazar comanda
                </button>
            </div>
        </div>
        <ValoracionForm v-if="aValorar" :id="aValorar" @enviar-valoracion="postValoracion(aValorar, $event)"
            @cerrar="aValorar = null" />
    </div>

    <div class="historial">
        <div class="titulo-historial">
            <img src="../assets/iconos/aceptar.png" alt="Historial" class="icono-titulo">
            <h3>Historial de comandas</h3>
        </div>

        <div v-if="historialComandas.length === 0" style="text-align: center; color: #999; padding: 20px;">
            No hay historial disponible.
        </div>

        <div v-for="item in historialComandas" :key="item.id" class="tarjeta-producto"
            :style="{ borderLeftColor: item.estado === 'cancelado' ? '#e74c3c' : '#22c55e' }">

            <div class="info-izquierda">
                <img :src="getUrlImagen(item.producto?.imagen)" alt="foto-producto" class="img-producto">
                <div class="detalles">
                    <h3>{{ item.producto?.nombre_producto }}</h3>
                    <div class="fila-datos">
                        <span>Cantidad: {{ item.cantidad }}</span>
                        <span class="separador">•</span>
                        <span class="precio">{{ item.precio_total }}€</span>
                        <span class="separador">•</span>
                        <span class="usuario">{{ item.comprador?.nombre_usuario }}</span>
                    </div>
                </div>
            </div>

            <div class="etiqueta-estado"
                :style="{ backgroundColor: item.estado === 'cancelado' ? '#e74c3c' : '#0f172a' }">
                {{ item.estado === 'cancelado' ? 'Rechazado' : 'Aceptado' }}
            </div>
        </div>

        <ValoracionForm v-if="aValorar" :id="aValorar" @enviar-valoracion="postValoracion(aValorar, $event)"
            @cerrar="aValorar = null" />
    </div>
    <Footer></Footer>
</template>

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
    background-color: #B9E2A6; /* De tu paleta */
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
    box-shadow: 0 4px 6px rgba(0,0,0,0.02);
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
}

.fila-datos {
    display: flex;
    gap: 8px;
    font-size: 0.9rem;
    color: #64748b;
    align-items: center;
}

.precio {
    color: #4CA626;
    font-weight: 700;
}

.separador {
    color: #cbd5e1;
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

.aceptar, .finalizar { background: #4CA626; color: white; }
.rechazar { background: #fee2e2; color: #e74c3c; border: 1px solid #e74c3c; }
.valorar { background: #3498db; color: white; }

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

.historial {
    margin-top: 50px;
    max-width: 90%;
    margin: auto;
}

.titulo-historial {
    margin-top: 50px;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.titulo-historial h3 {
    margin: 0;
    font-size: 1.2rem;
    color: #333;
}

.icono-titulo {
    width: 25px;
    height: 25px;
}

.tarjeta-producto {
    background-color: white;
    border: 1px solid #e2e8f0;
    border-left: 6px solid #22c55e;
    border-radius: 8px;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.info-izquierda {
    display: flex;
    align-items: center;
    gap: 15px;
}

.img-producto {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
}

.detalles h3 {
    margin: 0 0 5px 0;
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
}

.fila-datos {
    font-size: 14px;
    color: #64748b;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
}

.separador {
    font-size: 10px;
    color: #cbd5e1;
}

.precio {
    color: #22c55e;
    font-weight: 600;
}

.etiqueta-estado {
    background-color: #0f172a;
    color: white;
    padding: 6px 16px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 700;
    text-transform: capitalize;
    white-space: nowrap;
}

@media (max-width: 768px) {
    .comanda-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .comanda-actions {
        flex-direction: column;
    }

    .empty-header-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .badge-pendientes-empty {
        margin-left: 0;
    }
}
</style>