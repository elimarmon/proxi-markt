<script setup>
import { ref, watch, onMounted } from "vue";
import axios from "axios";

// id_receptor es el ID de la otra persona (calculado por el padre)
const props = defineProps(['id_receptor', 'id_producto', 'chatid', 'mi_id']);
const mensajes = ref([]);
const nuevoMensaje = ref("");

const cargarMensajes = async () => {
    if (!props.chatid) return;
    try {
        const token = localStorage.getItem('token');
        const res = await axios.get(`http://localhost:8080/api/chat/${props.chatid}`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        mensajes.value = res.data.mensajes;
    } catch (e) {
        console.error("Error al cargar mensajes:", e);
    }
};

const enviarMensaje = async () => {
    if (!nuevoMensaje.value.trim()) return;
    try {
        const token = localStorage.getItem('token');
        await axios.post('http://localhost:8080/api/enviarmensaje', {
            id_chat: props.chatid,
            id_vendedor: props.id_receptor, // Enviamos el mensaje al "otro"
            id_producto: props.id_producto,
            contenido: nuevoMensaje.value
        }, { headers: { Authorization: `Bearer ${token}` } });
        
        nuevoMensaje.value = "";
        cargarMensajes(); 
    } catch (e) {
        console.error("Error al enviar mensaje:", e);
    }
};

// Si el usuario cambia de chat en la lista, recargamos los mensajes
watch(() => props.chatid, cargarMensajes);

onMounted(cargarMensajes);
</script>

<template>
    <div class="chat-hijo">
        <div class="caja-mensajes">
            <div v-for="m in mensajes" :key="m.id" 
                 :class="['burbuja', m.id_envio === props.mi_id ? 'propio' : 'ajeno']">
                {{ m.contenido }}
            </div>
        </div>
        <div class="input-area">
            <input v-model="nuevoMensaje" @keyup.enter="enviarMensaje" placeholder="Escribe algo..." />
            <button @click="enviarMensaje">Enviar</button>
        </div>
    </div>
</template>

<style scoped>
.chat-hijo {
    display: flex;
    flex-direction: column;
    height: 100%; /* Ocupa todo el espacio que le da el padre */
    background-color: #ffffff;
}

/* Área de los mensajes con scroll */
.caja-mensajes {
    flex: 1; /* Empuja el input hacia abajo */
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background-color: #fcfcfc;
}

/* Burbujas de mensaje */
.burbuja {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 18px;
    font-size: 0.95rem;
    line-height: 1.4;
    word-wrap: break-word;
}

.propio {
    align-self: flex-end;
    background-color: #4ca626;
    color: white;
    border-bottom-right-radius: 4px;
    box-shadow: 0 2px 4px rgba(76, 166, 38, 0.2);
}

.ajeno {
    align-self: flex-start;
    background-color: #e9e9eb;
    color: #333;
    border-bottom-left-radius: 4px;
}

/* Área de entrada de texto fija abajo */
.input-area {
    padding: 15px 20px;
    background-color: #ffffff;
    border-top: 1px solid #eeeeee;
    display: flex;
    gap: 10px;
    align-items: center;
}

.input-area input {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid #dddddd;
    border-radius: 25px;
    outline: none;
    font-size: 0.9rem;
    transition: border-color 0.3s;
}

.input-area input:focus {
    border-color: #4ca626;
}

.input-area button {
    background-color: #4ca626;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 20px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}

.input-area button:hover {
    background-color: #3d8a1e;
}

/* Scrollbar estética */
.caja-mensajes::-webkit-scrollbar {
    width: 6px;
}

.caja-mensajes::-webkit-scrollbar-thumb {
    background-color: #cccccc;
    border-radius: 10px;
}
</style>