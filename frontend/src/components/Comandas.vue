<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import NavBar from "./NavBar.vue";
import ValoracionForm from "./ValoracionForm.vue";
import { useAuth } from "@/composables/useAuth";

const comandas = ref([]);
const cargando = ref(true);
const { usuario, fetchUsuario } = useAuth();
const token = localStorage.getItem("token");
const aValorar = ref(null);

const obtenerComandas = async () => {
    try {
        const response = await axios.get("http://localhost:8080/api/miscomandas", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        });
        comandas.value = response.data.datos;
    } catch (error) {
        console.error("Error:", error);
    } finally {
        cargando.value = false;
    }
};

const actualizarComanda = async (id, nuevoEstado) => {
    try {
        await axios.put(`http://localhost:8080/api/miscomandas/${id}`,
            {
                estado: nuevoEstado
            },
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json"
                }
            });
        obtenerComandas();
    } catch (err) {
        alert("Algo ha ido mal.");
        console.error(err);
    }
}

const abrirModalValoracion = (id) => {
    aValorar.value = id;
};

onMounted(async () => {
    if (!usuario) await fetchUsuario();
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
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
    } catch (err) {
        alert("Algo ha ido mal.")
        console.log(err);
    }
};

</script>

<template>
    <NavBar />
    <div class="contenedor-pagina">
        <div id="contenedor-titulo">
            <h1 class="titulo">Comandas</h1>
            <p class="subtitulo">
                Gestiona las solicitudes de compra de tus productos
            </p>
        </div>
        <div class="contenedor-comandas">
            <img src="../assets/iconos/stock.png" alt="Comandas pendientes" class="icono" />
            <h3>Comandas pendientes</h3>
            <p>{{ comandas.length }} pendientes</p>

            <p v-if="cargando">Cargando comandas...</p>

            <div v-if="!cargando && comandas.length === 0" class="sin-comandas-texto">
                <p>No hay comandas pendientes</p>
            </div>

            <div v-for="comanda in comandas" :key="comanda.id" class="comanda">
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
            <ValoracionForm v-if="aValorar" :id="aValorar" @enviar-valoracion="postValoracion(aValorar, $event)" @cerrar="aValorar = null" />
        </div>
    </div>
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

.contenedor-comandas {
    max-width: 90%;
    margin: auto;
    margin-top: 40px;
    display: flow-root;
}

.contenedor-comandas>.icono {
    width: 40px;
    height: 40px;
    vertical-align: middle;
    margin-right: 10px;
}

.contenedor-comandas h3 {
    display: inline-block;
    font-size: 1.4rem;
    color: #333333;
    vertical-align: middle;
    font-weight: bold;
}

.contenedor-comandas>p:nth-of-type(1) {
    float: right;
    background-color: #ffeada;
    color: #ff7519;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 1rem;
}

.comanda {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    padding: 15px 30px;
    border: 1px solid #eaeaea;
    border-left: 7px solid #ff7519;
    margin-top: 25px;
    display: grid;
    grid-template-columns: 130px 1fr auto;
    gap: 15px 30px;
    align-items: center;
}

.foto-producto {
    width: 100%;
    object-fit: cover;
    border-radius: 12px;
    grid-column: 1;
    grid-row: 1 / span 3;
    align-self: start;
}

#estado {
    grid-column: 2;
    grid-row: 2;
    display: inline-block;
    width: fit-content;
    font-size: 15px;
    background: #fff4e6;
    color: #ff7519;
    padding: 2px 6px;
    border-radius: 4px;
    align-self: start;
    margin-top: 5px;
}

.sin-comandas-texto {
    text-align: center;
    margin-top: 50px;
    color: #999999;
    font-size: 1.2rem;
    font-weight: 500;
    letter-spacing: 0.5px;
    padding: 30px;
    border: 1px solid #eeeeee;
    border-radius: 12px;
    background-color: #fafafa;
}

.comanda>p:nth-of-type(2) {
    display: inline-flex;
    align-items: center;
    background-color: #ffeada;
    color: #ff7519;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    width: fit-content;
    grid-column: 2;
    grid-row: 2;
    align-self: start;
}

.comanda>p:nth-of-type(2)::before {
    display: inline-block;
    margin-right: 8px;
    vertical-align: middle;
    height: 16px;
}

#precio-total {
    grid-column: 3;
    grid-row: 1 / span 2;
    text-align: right;
    align-self: center;
}

#precio-total p:first-child {
    color: #4ca626;
    font-size: 2rem;
    font-weight: bold;
    line-height: 1;
}

#precio-total p:last-child {
    color: #999999;
    font-size: 0.9rem;
    margin-top: 5px;
}

#cantidad,
#horario,
#usuario {
    grid-column: 2 / span 2;
    grid-row: 3;
    display: inline-flex;
    align-items: center;
    color: #333333;
    font-size: 0.95rem;
    background: #ffffff;
}

#cantidad {
    margin-right: auto;
}

#horario {
    margin-left: 140px;
}

#usuario {
    margin-left: 300px;
}

#cantidad .icono,
#horario .icono,
#usuario .icono {
    width: 18px;
    height: 18px;
    margin-right: 8px;
}

.mensaje-comprador {
    grid-column: 1 / span 3;
    grid-row: 4;
    background-color: #f0f7ff;
    padding: 20px;
    border-radius: 10px;
    margin-top: 10px;
}

.mensaje-comprador .icono {
    width: 30px;
    height: 30px;
    vertical-align: middle;
    margin-right: 10px;
}

.mensaje-comprador p:nth-child(2) {
    display: inline-block;
    color: #007bff;
    font-weight: bold;
    margin-bottom: 8px;
}

.mensaje-comprador p:last-child {
    display: block;
    color: #333333;
    margin-left: 34px;
    font-style: italic;
}

.comanda>button {
    padding: 14px;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.2s ease;
    height: 55px;
    grid-row: 5;
}

.comanda>button .icono {
    width: 25px;
    height: 25px;
}

.aceptar {
    grid-column: 1 / -1;
    width: calc(50% - 10px);
    justify-self: start;
    background: linear-gradient(90deg, #4ca626 0%, #009b58 100%);
    color: white;
    border: none;
}

.aceptar:hover {
    background: linear-gradient(90deg, #008f4c 0%, rgb(1, 104, 59) 100%);
}

.rechazar {
    grid-column: 1 / -1;
    width: calc(50% - 10px);
    justify-self: end;
    background-color: white;
    color: #e74c3c;
    border: 2px solid #e74c3c;
}

.rechazar:hover {
    background-color: #ffdddd;
}
</style>
