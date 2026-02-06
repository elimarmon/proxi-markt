<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import NavBar from "./NavBar.vue";
import ChatDetalle from "./Chat.vue";

const chats = ref([]);
const chatSeleccionadoId = ref(null);
const idUsuarioLogueado = ref(null);

// saber si eres comprador o vendedor
const obtenerDatosUsuario = async () => {
    try {
        const token = localStorage.getItem('token');
        const res = await axios.get('http://localhost:8080/api/datosuser', {
            headers: { Authorization: `Bearer ${token}` }
        });
        idUsuarioLogueado.value = res.data.id;
    } catch (error) {
        console.error("Error obteniendo usuario:", error);
    }
};

// esta funcio es pa pasarli al fill quin chat has seleccionat
const chatActivo = computed(() => {
    return chats.value.find(c => c.id === chatSeleccionadoId.value);
});

// comprobar qui es qui (vendedor, comprador)
const idReceptorDinamico = computed(() => {
    if (!chatActivo.value || !idUsuarioLogueado.value) return null;

    if (chatActivo.value.id_vendedor === idUsuarioLogueado.value) {
        // si el id del vendedor del chat es el meu, jo soc el vendedor
        return chatActivo.value.id_comprador;
    } else {
        // Si no soc el comprador
        return chatActivo.value.id_vendedor;
    }
});

const obtenerChats = async () => {
    try {
        const token = localStorage.getItem('token');
        const respuesta = await axios.get('http://localhost:8080/api/mis-chats', {
            headers: { Authorization: `Bearer ${token}` }
        });
        chats.value = respuesta.data;
    } catch (error) {
        console.error("Error obteniendo chats:", error);
    }
}

onMounted(() => {
    obtenerChats();
    obtenerDatosUsuario();
});
</script>

<template>
    <NavBar />
    <div class="contenedor-pagina">
        <div id="layout-chat">
            <div class="lista-chats">
                <div v-for="chat in chats" :key="chat.id" @click="chatSeleccionadoId = chat.id"
                    :class="['item-chat', { activo: chatSeleccionadoId === chat.id }]">
                    <h3>
                        {{ chat.id_vendedor === idUsuarioLogueado ? chat.comprador.nombre_usuario :
                            chat.vendedor.nombre_usuario }}
                    </h3>
                    <p>{{ chat.producto.nombre_producto }}</p>
                </div>
            </div>

            <div class="ventana-mensajes">
                <!-- si es selecciona un chat i el usuari esta logejat
                 enviem al component fill el qui el recibix, 
                 el producte, y el id de la persona logejada   -->
                <ChatDetalle v-if="chatActivo && idUsuarioLogueado" :id_receptor="idReceptorDinamico"
                    :id_producto="chatActivo.id_producto" :chatid="chatActivo.id" :mi_id="idUsuarioLogueado" />
                <div v-else class="vacio">
                    <p>Selecciona una conversación</p>
                </div>
            </div>
        </div>
    </div>
</template>


<style scoped>
.contenedor-pagina {
    padding: 50px;
    padding-top: 130px;
    height: 100vh;
    box-sizing: border-box;
    background-color: #f5f5f5;
}

#layout-chat {
    display: grid;
    grid-template-columns: 350px 1fr;
    height: 75vh;
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    max-width: 95%;
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