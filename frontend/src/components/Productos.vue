<script setup>
import { ref, onMounted, watch, computed } from "vue";
import axios from "axios";
import NavBar from "./NavBar.vue";
import { useAuth } from '@/composables/useAuth';
import MostrarProductos from './MostrarProductosMain.vue';

const productos = ref([]);
const radioActual = ref(
    localStorage.getItem('distancia_guardada') === 'Infinity'
        ? Infinity
        : (Number(localStorage.getItem('distancia_guardada')) || 10)
);
const { usuario, fetchUsuario } = useAuth();
const categoriasSeleccionadas = ref([]);
const menuAbierto = ref(false);
const cargando = ref(false);
const textoBusqueda = ref("");
const pagination = ref({});
const paginaActual = ref(1);

// TODO: se calculan distancias en el padre y en el hijo-> intentar simplificarlo
const calcularDistanciaReal = (latV, lngV) => {
    if (!usuario.value || !usuario.value.latitud || !latV || !lngV) {
        return 999999;
    }

    const miLat = parseFloat(usuario.value.latitud);
    const miLng = parseFloat(usuario.value.longitud);
    const vLat = parseFloat(latV);
    const vLng = parseFloat(lngV);

    const p = Math.PI / 180;
    const c = Math.cos;
    const a = 0.5 - c((vLat - miLat) * p) / 2 +
        c(miLat * p) * c(vLat * p) * (1 - c((vLng - miLng) * p)) / 2;

    return 12742 * Math.asin(Math.sqrt(a));
};

const manejarCambioRadio = (nuevoRadio) => {
    radioActual.value = nuevoRadio;
    localStorage.setItem('distancia_guardada', nuevoRadio);
    mostrarProductos();
};

const categorias = computed(() => {
    const nombres = productos.value.map(p => p.categoria ? p.categoria.nombre_categoria : null);
    const unicas = [...new Set(nombres)].filter(Boolean);
    return unicas.sort();
});

const mostrarProductos = async (pagina = 1) => {
    cargando.value = true;
    const token = localStorage.getItem('token');
    const radioParaAPI = radioActual.value === Infinity ? 99999 : radioActual.value;

    try {
        const response = await axios.get("http://localhost:8080/api/productos", {
            params: {
                km: radioParaAPI,
                page: pagina
            },
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        productos.value = response.data.data;
        pagination.value = response.data;
        paginaActual.value = response.data.current_page;


    } catch (error) {
        console.error("Error al cargar productos:", error);
    } finally {
        cargando.value = false;
    }
};

const productosFiltrados = computed(() => {
    return productos.value.filter(p => {
        const coincideTexto = !textoBusqueda.value ||
            p.nombre_producto.toLowerCase().includes(textoBusqueda.value.toLowerCase());

        const coincideCategoria = categoriasSeleccionadas.value.length === 0 ||
            categoriasSeleccionadas.value.includes(p.categoria?.nombre_categoria);

        const dist = calcularDistanciaReal(p.punto_entrega?.latitud, p.punto_entrega?.longitud);
        const coincideRadio = radioActual.value === Infinity || dist <= radioActual.value;

        return coincideTexto && coincideCategoria && coincideRadio;
    });
});

const toggleMenu = () => {
    menuAbierto.value = !menuAbierto.value;
}

onMounted(async () => {
    await fetchUsuario();
    mostrarProductos();
});
</script>

<template>
    <NavBar @cambiar-radio="manejarCambioRadio"/>
    <div class="contenedor-pagina">
        <div class="zona-fija">
            <h1 class="titulo-verde">Productos Frescos y Locales</h1>
            <p class="subtitulo">Conecta directamente con productores de tu zona (radio: {{ radioActual === Infinity ?
                '∞' : radioActual }} km)</p>

            <div class="card-busqueda">
                <div id="buscador">
                    <div class="caja-busqueda">
                        <img src="../assets/iconos/buscar.png" alt="lupa" class="icono-pequeno" />
                        <input v-model="textoBusqueda" class="input-texto" type="text"
                            placeholder="Buscar productos frescos..." />
                    </div>

                    <div class="caja-filtro-especial">
                        <button class="boton-secundario"
                            :class="{ 'activo': menuAbierto || categoriasSeleccionadas.length > 0 }"
                            @click="toggleMenu">
                            <img src="../assets/iconos/filtro.png" alt="filtro" class="icono-pequeno" />
                            <span>
                                {{ categoriasSeleccionadas.length > 0 ? `Filtros (${categoriasSeleccionadas.length})` :
                                    'Filtros' }}
                            </span>
                        </button>

                        <div v-if="menuAbierto" class="menu-checkboxes">
                            <label v-for="cat in categorias" :key="cat" class="fila-opcion">
                                <input type="checkbox" :value="cat" v-model="categoriasSeleccionadas">
                                <span class="nombre-cat">{{ cat }}</span>
                            </label>
                        </div>

                    </div>
                </div>
                <p class="informacion-resultados">
                    {{ productosFiltrados.length }} productos encontrados
                    <span class="texto-verde">
                        ({{ radioActual === Infinity ? 'sin límite' : 'en un radio de ' + radioActual + ' km' }})
                    </span>
                </p>
            </div>
        </div>

        <div v-if="cargando" style="text-align: center; padding: 20px;">
            <p>Cargando productos...</p>
        </div>
        <MostrarProductos v-else-if="productosFiltrados.length >= 1" :productos="productosFiltrados"
            :radioMaximo="radioActual"></MostrarProductos>
        <div v-else class="mensaje-ayuda">
            <p>No se han encontrado productos.</p>
        </div>
    </div>
    <div v-if="pagination.last_page > 1" class="paginacion">

        <button :disabled="paginaActual === 1" @click="mostrarProductos(paginaActual - 1)"> Anterior </button>

        <span>Página {{ paginaActual }} de {{ pagination.last_page }}</span>

        <button :disabled="paginaActual === pagination.last_page" @click="mostrarProductos(paginaActual + 1)"> Siguiente
        </button>
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
    max-width: 1200px;
    margin: 0 auto;
    padding: 380px 40px 40px;
    font-family: 'Segoe UI', 'Arial';
}

.titulo-verde {
    color: #4CA626;
    font-size: 2rem;
    margin-bottom: 5px;
    font-weight: bold;
}

.subtitulo {
    color: #666666;
    margin-bottom: 30px;
}

.zona-fija {
    position: fixed;
    top: 80px;
    left: 0;
    width: 100%;
    height: auto;
    background-color: white;
    z-index: 900;
    box-shadow: 0 10px 20px -10px rgba(255, 255, 255, 1);
    padding-top: 20px;
    padding-left: max(40px, calc((100% - 1200px) / 2 + 40px));
    padding-right: max(40px, calc((100% - 1200px) / 2 + 40px));
    box-sizing: border-box;
}

.mensaje-ayuda,
.mensaje-informativo {
    margin: 40px auto;
    text-align: center;
    color: #999;
    font-style: italic;
    font-size: 1.1rem;
    padding: 20px;
    background: #fdfdfd;
    border-radius: 8px;
}

.card-busqueda {
    background: white;
    border: 1px solid #EEEEEE;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

#buscador {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 10px;
}

.caja-busqueda {
    background-color: #F9F9F9;
    border-radius: 8px;
    padding: 12px 15px;
    display: flex;
    align-items: center;
    flex-grow: 1;
    border: 1px solid #DDDDDD;
}

.input-texto {
    border: none;
    background: transparent;
    width: 100%;
    margin-left: 10px;
    outline: none;
    font-size: 16px;
    color: #333333;
}

.icono-pequeno {
    width: 20px;
    height: 20px;

}

.informacion-resultados {
    font-size: 0.9rem;
    color: #666666;
    padding-left: 5px;
}

.texto-verde {
    color: #4CA626;
}

.caja-filtro-especial {
    position: relative;
}

.boton-secundario {
    background-color: white;
    border: 1px solid #DDDDDD;
    border-radius: 8px;
    padding: 12px 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: #333;
    transition: all 0.2s ease;
    min-width: 140px;
    justify-content: space-between;
}

.boton-secundario:hover {
    background-color: #f8f8f8;
    border-color: #ccc;
}

.boton-secundario.activo {
    border-color: #4CA626;
    background-color: #f0fdf4;
    color: #4CA626;
}

.flecha {
    font-size: 10px;
    color: #999;
    transition: transform 0.2s ease;
}

.flecha.rotada {
    transform: rotate(180deg);
}

.menu-checkboxes {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 260px;
    background: white;
    border: 1px solid #eee;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    z-index: 1000;
    overflow: hidden;
    animation: fadeIn 0.2s ease-out;
}

.menu-header {
    padding: 12px 16px;
    background: #fcfcfc;
    border-bottom: 1px solid #eee;
    font-size: 13px;
    font-weight: 600;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.lista-opciones {
    padding: 8px;
    max-height: 300px;
    overflow-y: auto;
}

.fila-opcion {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.2s;
    user-select: none;
}

.fila-opcion:hover {
    background-color: #f0fdf4;
}

.nombre-cat {
    font-size: 15px;
    color: #333;
}

.fila-opcion input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    width: 18px;
    height: 18px;
    border: 2px solid #ccc;
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.fila-opcion input[type="checkbox"]:checked {
    background-color: #4CA626;
    border-color: #4CA626;
}

.fila-opcion input[type="checkbox"]:checked::after {
    content: '✔';
    font-size: 12px;
    color: white;
    font-weight: bold;
}

.menu-footer {
    padding: 10px;
    border-top: 1px solid #eee;
    text-align: right;
}

.limpiar-texto {
    font-size: 13px;
    color: #e53935;
    cursor: pointer;
    font-weight: 500;
}

.limpiar-texto:hover {
    text-decoration: underline;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .contenedor-pagina {
        padding: 0 20px 20px 20px;
    }

    #buscador {
        flex-direction: column;
        align-items: stretch;
    }

    .menu-checkboxes {
        width: 100%;
    }
}

.paginacion {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin: 20px 0px 40px 0px;
    padding: 10px;
}

.paginacion button {
    padding: 8px 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
    background: white;
    cursor: pointer;
}

.paginacion button:disabled {
    background: #eee;
    cursor: not-allowed;
}
</style>