<script setup>
import { ref, onMounted, computed } from "vue";
import api from "@/api/axios";
import { useAuth } from '@/composables/useAuth';
import NavBar from "./NavBar.vue";
import ChatDetalle from "./Chat.vue";
import Footer from "./Footer.vue";

const chats = ref([]);
const chatSeleccionadoId = ref(null);
const { usuario, fetchUsuario } = useAuth();

// esta funcio es pa pasarli al fill quin chat has seleccionat
const chatActivo = computed(() => {
    return chats.value.find(c => c.id === chatSeleccionadoId.value);
});

// comprobar qui es qui (vendedor, comprador)
const idReceptorDinamico = computed(() => {
    if (!chatActivo.value || !usuario.value?.id) return null;

    if (chatActivo.value.id_vendedor === usuario.value.id) {
        // si el id del vendedor del chat es el meu, jo soc el vendedor
        return chatActivo.value.id_comprador;
    } else {
        // Si no soc el comprador
        return chatActivo.value.id_vendedor;
    }
});

const obtenerChats = async () => {
    try {
        const respuesta = await api.get('/mis-chats');
        chats.value = respuesta.data;
    } catch (error) {
        console.error("Error obteniendo chats:", error);
    }
}

onMounted(async () => {
    await fetchUsuario();
    if (usuario.value?.id) {
        obtenerChats();
    }
});
</script>

<template>
    <NavBar />
    <div class="contenedor-pagina">
        <div id="contenedor-titulo">
            <h1 class="titulo">Mensajes</h1>
            <p class="subtitulo">Conversaciones con compradores y vendedores</p>
        </div>
        <div id="layout-chat">
            <div class="lista-chats">
                <div v-for="chat in chats" :key="chat.id" @click="chatSeleccionadoId = chat.id"
                    :class="['item-chat', { activo: chatSeleccionadoId === chat.id }]">
                    <h3>
                        {{ chat.id_vendedor === usuario.id ? chat.comprador?.nombre_usuario :
                            chat.vendedor.nombre_usuario }}
                    </h3>
                    <p>{{ chat.producto.nombre_producto }}</p>
                </div>
            </div>

            <div class="ventana-mensajes">
                <!-- si es selecciona un chat i el usuari esta logejat
                 enviem al component fill el qui el recibix, 
                 el producte, y el id de la persona logejada   -->
                <ChatDetalle v-if="chatActivo && usuario?.id" :id_receptor="idReceptorDinamico"
                    :id_producto="chatActivo.id_producto" :chatid="chatActivo.id" :mi_id="usuario.id" />
                <div v-else class="vacio">
                    <p>Selecciona una conversación</p>
                </div>
            </div>
        </div>
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
    padding: 20px 50px;
    padding-top: 130px;
    min-height: 100vh; 
    height: auto;
    box-sizing: border-box;
    background-color: #f5f5f5;
    padding-bottom: 40px; 
}

#contenedor-titulo {
    max-width: 90%;
    margin: 10px auto 0 auto;
}

.titulo {
    font-family: sans-serif;
    color: #4CA626;
    margin-bottom: 10px;
    font-weight: bold;
}

.subtitulo {
    font-family: sans-serif;
    color: #666666;
    margin-bottom: 20px;
}

#layout-chat {
    display: grid;
    grid-template-columns: 350px 1fr;
    height: 80vh;
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    max-width: 90%;
    margin: 20px auto;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.lista-chats {
    border-right: 1px solid #f0f0f0;
    overflow-y: auto;
    background: #fdfdfd;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.item-chat {
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    background-color: white;
}

.item-chat:hover {
    background-color: #f9f9f9;
    border-color: #ddd;
}

.item-chat.activo {
    background-color: #4ca626;
    border-color: #4ca626;
    color: white;
}

.item-chat h3 {
    margin: 0 0 5px 0;
    font-size: 1rem;
}

.item-chat p {
    margin: 0;
    font-size: 0.85rem;
    opacity: 0.8;
}

.ventana-mensajes {
    display: flex;
    flex-direction: column;
    background: #ffffff;
    height: 100%;
    overflow: hidden;
    position: relative;
}

.vacio {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #999;
}
</style>