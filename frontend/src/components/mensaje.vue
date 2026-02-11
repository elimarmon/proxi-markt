<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import axios from "axios";
import NavBar from "./NavBar.vue";
import ChatDetalle from "./chat.vue"; 

const chats = ref([]);
const chatSeleccionadoId = ref(null);
const idUsuarioLogueado = ref(null); 
let intervaloChat = null; // Variable para el auto-refresco

// --- LÓGICA DE USUARIO ---
const obtenerDatosUsuario = async () => {
    try {
        const token = localStorage.getItem('token');
        if(!token) return;
        const res = await axios.get('http://localhost:8080/api/datosuser', {
            headers: { Authorization: `Bearer ${token}` }
        });
        idUsuarioLogueado.value = res.data.id;
    } catch (error) {
        console.error("Error obteniendo usuario:", error);
    }
};

// --- COMPUTADAS ---
const chatActivo = computed(() => {
    return chats.value.find(c => c.id === chatSeleccionadoId.value);
});

const idReceptorDinamico = computed(() => {
    if (!chatActivo.value || !idUsuarioLogueado.value) return null;
    return chatActivo.value.id_vendedor === idUsuarioLogueado.value 
        ? chatActivo.value.id_comprador 
        : chatActivo.value.id_vendedor;
});

const hayNotificacionesGlobales = computed(() => {
    if (!chats.value) return false;
    return chats.value.some(chat => chat.mensajes_no_leidos > 0);
});

// --- API CHATS ---
const obtenerchats = async () => {
    try {
        const token = localStorage.getItem('token');
        if(!token) return;

        const respuesta = await axios.get('http://localhost:8080/api/mischats', {
            headers: { Authorization: `Bearer ${token}` }
        });
        chats.value = respuesta.data;
    } catch (error) {
        // Ignoramos errores de auth en el polling
        if (error.response && error.response.status !== 401) {
            console.error("Error obteniendo chats:", error);
        }
    }
}

// --- NUEVA FUNCIÓN: SELECCIONAR Y MARCAR COMO LEÍDO ---
const seleccionarChat = async (chatId) => {
    // 1. Cambiamos la selección visualmente
    chatSeleccionadoId.value = chatId;

    // 2. Buscamos el chat en la lista
    const chat = chats.value.find(c => c.id === chatId);

    // 3. Si tiene mensajes sin leer, los limpiamos
    if (chat && chat.mensajes_no_leidos > 0) {
        // Truco visual: quitar el punto rojo inmediatamente
        chat.mensajes_no_leidos = 0;

        try {
            const token = localStorage.getItem('token');
            // Avisar a Laravel
            await axios.put(`http://localhost:8080/api/chats/${chatId}/leer`, {}, {
                 headers: { Authorization: `Bearer ${token}` }
            });
        } catch (e) {
            console.error("Error marcando leído:", e);
        }
    }
}

onMounted(() => {
    obtenerchats();
    obtenerDatosUsuario();
    // Auto-refresco cada 3 segundos para ver si llegan mensajes nuevos
    intervaloChat = setInterval(obtenerchats, 3000);
});

onUnmounted(() => {
    if (intervaloChat) clearInterval(intervaloChat);
});
</script>

<template>
    <NavBar :tiene-notificacion="hayNotificacionesGlobales"/>

    <div class="contenedor-pagina">
        <div id="layout-chat">
            
            <div class="lista-chats">
                <div v-for="chat in chats" :key="chat.id" 
                     @click="seleccionarChat(chat.id)"
                     :class="['item-chat', { activo: chatSeleccionadoId === chat.id }]">
                    
                    <div v-if="chat.mensajes_no_leidos > 0" class="punto-rojo"></div>

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
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
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
    
    /* IMPORTANTE: Relative para poder posicionar el punto rojo dentro */
    position: relative; 
}

/* ESTILO DEL PUNTO ROJO */
.punto-rojo {
    position: absolute;
    top: 10px;
    right: 10px;
    /* Hacemos el círculo un poco más grande (antes era 18px) */
    min-width: 22px; 
    height: 22px;
    
    padding: 0 4px;
    background-color: #ff3b30;
    color: white;
    font-size: 11px;
    font-weight: bold;
    border-radius: 12px; /* Ajustamos el radio para que siga siendo redondo */
    display: flex;
    align-items: center;
    justify-content: center;
    
    /* HE BORRADO EL BORDE BLANCO (border: 2px solid white;) */
    border: none; 
    
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    z-index: 10;
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