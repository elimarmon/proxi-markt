<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import navbar from "./nav.vue";
import ChatDetalle from "./chat.vue"; 

const chats = ref([]);
const chatSeleccionadoId = ref(null);
const idUsuarioLogueado = ref(null); // Guardamos quién eres tú

// 1. Obtenemos tu ID para saber si eres el comprador o vendedor
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

const chatActivo = computed(() => {
    return chats.value.find(c => c.id === chatSeleccionadoId.value);
});

// 2. EL IF QUE NECESITABAS: Calculamos quién es la otra persona
const idReceptorDinamico = computed(() => {
    if (!chatActivo.value || !idUsuarioLogueado.value) return null;

    if (chatActivo.value.id_vendedor === idUsuarioLogueado.value) {
        // Si mi ID es el del vendedor, el receptor es el comprador
        return chatActivo.value.id_comprador;
    } else {
        // Si no soy el vendedor, el receptor es el vendedor
        return chatActivo.value.id_vendedor;
    }
});

const obtenerchats = async () => {
    try {
        const token = localStorage.getItem('token');
        const respuesta = await axios.get('http://localhost:8080/api/mischats', {
            headers: { Authorization: `Bearer ${token}` }
        });
        chats.value = respuesta.data;
    } catch (error) {
        console.error("Error obteniendo chats:", error);
    }
}

onMounted(() => {
    obtenerchats();
    obtenerDatosUsuario();
});
</script>

<template>
    <navbar></navbar>
    <div class="contenedor-pagina">
        <div id="layout-chat">
            <div class="lista-chats">
                <div v-for="chat in chats" :key="chat.id" 
                     @click="chatSeleccionadoId = chat.id"
                     :class="['item-chat', { activo: chatSeleccionadoId === chat.id }]">
                    <h3>
                        {{ chat.id_vendedor === idUsuarioLogueado ? chat.comprador.nombre_usuario : chat.vendedor.nombre_usuario }}
                    </h3>
                    <p>{{ chat.producto.nombre_producto }}</p>
                </div>
            </div>

            <div class="ventana-mensajes">
                <ChatDetalle 
                    v-if="chatActivo && idUsuarioLogueado" 
                    :id_receptor="idReceptorDinamico" 
                    :id_producto="chatActivo.id_producto" 
                    :chatid="chatActivo.id"
                    :mi_id="idUsuarioLogueado"
                />
                <div v-else class="vacio">
                    <p>Selecciona una conversación</p>
                </div>
            </div>
        </div>
    </div>
</template>


<style scoped>
.contenedor-pagina{
    padding: 50px;
    padding-top: 130px;
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
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.lista-chats {
    border-right: 1px solid #f0f0f0;
    overflow-y: auto;
    background: #fdfdfd;
}

.item-chat {
    padding: 18px;
    border-bottom: 1px solid #f5f5f5;
    cursor: pointer;
    transition: all 0.2s ease;
}

.item-chat:hover {
    background: #f1f8e9;
}

.item-chat.activo {
    background: #e8f5e9;
    border-left: 5px solid #4ca626;
}

.item-chat h3 {
    font-size: 1rem;
    color: #333;
    margin-bottom: 4px;
}

.item-chat p {
    font-size: 0.85rem;
    color: #777;
}

.ventana-mensajes {
    display: flex;
    flex-direction: column;
    background: #ffffff;
    position: relative;
}

.header-chat {
    padding: 15px 25px;
    background: #fafafa;
    border-bottom: 1px solid #eeeeee;
}

.header-chat h2 {
    font-size: 1.1rem;
    color: #4ca626;
}

.caja-mensajes {
    flex: 1;
    padding: 25px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background-image: linear-gradient(to bottom, #ffffff, #f9f9f9);
}

.burbuja {
    max-width: 65%;
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 0.95rem;
    line-height: 1.4;
    position: relative;
}

.propio {
    align-self: flex-end;
    background: #4ca626;
    color: white;
    border-bottom-right-radius: 4px;
}

.ajeno {
    align-self: flex-start;
    background: #f1f1f1;
    color: #444;
    border-bottom-left-radius: 4px;
}

.vacio {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #bbbbbb;
    font-style: italic;
    text-align: center;
}
</style>
