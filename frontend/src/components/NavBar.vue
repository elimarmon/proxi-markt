<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import ModalRadio from './ModalRadio.vue';
import { useAuth } from '@/composables/useAuth';

const router = useRouter();
const mostrarMenu = ref(false);
const { usuario, fetchUsuario, estarAutenticado, logout } = useAuth();

const cerrarSesion = () => {
    logout();
    router.push('/');
};

const isModalOpen = ref(false);
const emit = defineEmits(['cambiar-radio']);

const radioActual = ref(Number(localStorage.getItem('distancia_guardada')) || 10);

const confirmarNuevoRadio = (valor) => {
    radioActual.value = valor;
    localStorage.setItem('distancia_guardada', valor);
    isModalOpen.value = false;
    emit('cambiar-radio', valor);
    // console.log("Filtrando productos a:", valor === Infinity ? "Sin límite" : valor + " km");
};

const irAuth = (modo) => {
    router.push({ name: 'auth', state: { modo: modo } });
}

onMounted(async () => {
    if (estarAutenticado.value) await fetchUsuario();
});
</script>

<template>
    <header>
        <nav id="nav-contenedor">
            <div @click="router.push('/')" id="logo">
                <img src="../assets/logos/logo_peq.png" alt="Logo ProxiMarkt" />
                <div class="texto-logo">
                    <p class="titulo">ProxiMarkt</p>
                    <p class="subtitulo">Frutas y verduras frescas</p>
                </div>
            </div>

            <div v-if="estarAutenticado" class="nav-autenticado">
                <ul class="enlaces-paginas">
                    <li>
                        <router-link to="/dashboard">
                            <img class="logos-nav" src="../assets/iconos/dashboard_verde.png" alt="icon">
                            <span>Dashboard</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/productos">
                            <img class="logos-nav" src="../assets/iconos/productos_verde.png" alt="icon">
                            <span>Productos</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/mapa">
                            <img class="logos-nav" src="../assets/iconos/ubicacion.png" alt="icon">
                            <span>Mapa</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/mensaje">
                            <img class="logos-nav" src="../assets/iconos/chat_verde.png" alt="icon">
                            <span>Mensajes</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/comandas">
                            <img class="logos-nav" src="../assets/iconos/comandas.png" alt="icon">
                            <span>Comandas</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/publicar">
                            <img class="logos-nav" src="../assets/iconos/publicar_verde.png" alt="icon">
                            <span>Publicar</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/cuenta">
                            <img class="logos-nav" src="../assets/iconos/mi_cuenta_verde.png" alt="icon">
                            <span>Mi cuenta</span>
                        </router-link>
                    </li>
                </ul>

                <div class="utilidades-usuario">
                    <div id="radio_busqueda" @click="isModalOpen = true">
                        <img src="../assets/iconos/tuerca_verde.png" alt="radio">
                        <span :class="{ 'simbolo-infinito-nav': radioActual === Infinity }">
                            {{ radioActual === Infinity ? '∞' : radioActual + 'km' }}
                        </span>
                    </div>

                    <div class="contenedor-perfil">
                        <div id="usuario" @click="mostrarMenu = !mostrarMenu">
                            <img src="../assets/iconos/cuenta.png" alt="perfil">
                            <span class="nombre-user">{{ usuario?.nombre_usuario || 'Usuario' }}</span>
                            <span class="triangulo"> ▼ </span>
                        </div>

                        <div v-if="mostrarMenu" class="menu-desplegable">
                            <ul>
                                <li @click="cerrarSesion" class="item-logout">
                                    <img src="../assets/iconos/rechazar.png" alt="salir">
                                    <span>Cerrar sesión</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="botones-acceso">
                <button @click="irAuth('login')" class="btn-nav btn-outline">Iniciar Sesión</button>
                <button @click="irAuth('register')" class="btn-nav btn-gradient">Registrarse</button>
            </div>
        </nav>

        <ModalRadio :mostrar="isModalOpen" :distanciaInicial="radioActual" @cerrar="isModalOpen = false"
            @confirmar="confirmarNuevoRadio" />
    </header>
</template>

<style scoped>
header {
    width: 100%;
    background-color: #ffffff;
    box-shadow: 0px 4px 15px -5px #CFCFCF;
    height: 80px;
    display: flex;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
}

#nav-contenedor {
    width: 100%;
    padding: 0 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

#logo {
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
    text-decoration: none !important;
}

#logo img {
    height: 50px;
    border-radius: 12px;
}

.titulo {
    font-size: 22px;
    font-weight: 700;
    color: #4CA626;
    margin: 0;
}

.subtitulo {
    font-size: 13px;
    color: #757575;
    margin: 0;
}

.nav-autenticado {
    display: flex;
    align-items: center;
    flex: 1;
    justify-content: space-between;
}

.enlaces-paginas {
    display: flex;
    list-style: none;
    gap: 5px;
    margin: 0 auto;
}

.enlaces-paginas li a {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #5F6368;
    font-size: 15px;
    font-weight: 600;
    padding: 8px 12px;
    border-radius: 10px;
}

.enlaces-paginas li a:hover,
.router-link-active {
    background-color: #4CA62615;
    color: #4CA626;
}

.logos-nav {
    width: 24px;
    height: 24px;
    margin-right: 8px;
}

.utilidades-usuario {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-shrink: 0;
}

#radio_busqueda {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 700;
    color: #5F6368;
    cursor: pointer;
}

#radio_busqueda img {
    width: 35px;
}

#usuario {
    display: flex;
    align-items: center;
    background-color: #4CA62615;
    padding: 6px 15px;
    border-radius: 50px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: #5F6368;
}

#usuario img {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    margin-right: 8px;
}

.botones-acceso {
    display: flex;
    gap: 12px;
}

.btn-nav {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    border: 2px solid transparent;
}

.btn-gradient {
    background: linear-gradient(90deg, #4CA626 0%, #009B58 100%);
    color: white;
}

.btn-outline {
    border-color: #4CA626;
    color: #4CA626;
    background: transparent;
}

.menu-desplegable {
    position: absolute;
    top: 100%;
    right: 5px;
    width: 180px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-top: 5px;
    border: 1px solid #eee;
}

.menu-desplegable ul {
    padding: 0;
    margin-top: 1rem;
}

.item-logout {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    cursor: pointer;
    color: #5F6368;
    font-size: 14px;
}

.item-logout img {
    width: 18px;
    height: 18px;
    object-fit: contain;
}

.item-logout:hover {
    background: #fff5f5;
    color: #d32f2f;
}

@media (max-width: 1200px) {

    .enlaces-paginas span,
    .subtitulo,
    .nombre-user {
        display: none;
    }

    .logos-nav {
        margin-right: 0;
        width: 28px;
        height: 28px;
    }
}

@media (max-width: 768px) {
    header {
        height: auto;
        padding: 10px 0;
    }

    #nav-contenedor {
        flex-wrap: wrap;
    }

    .nav-autenticado {
        flex-direction: column;
        width: 100%;
    }

    .enlaces-paginas {
        width: 100%;
        overflow-x: auto;
        padding: 10px 0;
        justify-content: flex-start;
        border-top: 1px solid #eee;
        margin-top: 10px;
    }

    .utilidades-usuario {
        width: 100%;
        justify-content: flex-end;
        margin-bottom: 5px;
    }
}
</style>