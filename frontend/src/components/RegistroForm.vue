<script setup>
import { ref } from "vue";
import api from "@/api/axios";
import { useAuth } from '@/composables/useAuth';
import { useRouter } from 'vue-router';

const toastVisible = ref(false);
const toastMensaje = ref("");

const lanzarToast = (mensaje) => {
    toastMensaje.value = mensaje;
    toastVisible.value = true;
    setTimeout(() => {
        toastVisible.value = false;
    }, 3000);
};

const form = ref({
    nombre_usuario: "",
    email: "",
    contrasenya: "",
    telefono: ""
});

const { loading, setLoading } = useAuth();
const router = useRouter();

const enviarInfo = async () => {

    const { nombre_usuario, email, contrasenya, telefono } = form.value;

    if (!nombre_usuario || !email || !contrasenya || !telefono) {
        lanzarToast("Rellena los campos obligatorios.");
        return;
    }

    setLoading(true);

    try {
        // console.log("Enviando registro: ", form.value);
        await api.post("/register", form.value);

        form.value = {
            nombre_usuario: "",
            email: "",
            contrasenya: "",
            telefono: ""
        };

        lanzarToast("¡Cuenta creada con éxito! Ahora puedes iniciar sesión.");
        setTimeout(() => {
            router.push('/login');
        }, 2000);

    } catch (error) {
        lanzarToast("Hubo un error al conectar con el servidor");
        console.error("Error en la petición:", error);
    } finally {
        setLoading(false);
    }
};
</script>

<template>
    <div class="form-card">
        <h3>Crear Cuenta</h3>
        <p class="subtitle">Únete a la comunidad de ProxiMarkt</p>

        <form @submit.prevent="enviarInfo">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input v-model="form.nombre_usuario" type="text" id="nombre" placeholder="Juan Garcia" />
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input v-model="form.email" type="email" id="email" placeholder="tu@gmail.com" />
            </div>

            <div class="form-group">
                <label for="contrasenya">Contraseña</label>
                <input v-model="form.contrasenya" type="password" id="contrasenya" placeholder="••••••••" />
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input v-model="form.telefono" type="tel" id="telefono" placeholder="123456789" />
            </div>

            <button type="submit" class="boton-submit" :disabled="loading">
                <span v-if="!loading">Crear Cuenta</span>
                <span v-else>Procesando...</span>
            </button>
        </form>
        <div v-if="toastVisible" class="toast-notificacion">
            {{ toastMensaje }}
        </div>
    </div>
</template>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.register-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    width: 100%;
    padding: 20px;
}

.form-card {
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 35px 30px;
    text-align: left;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    width: 100%;
}

h3 {
    font-size: 1.3rem;
    font-weight: 700;
    margin-top: 0;
    margin-bottom: 8px;
    color: #111827;
}

.subtitle {
    color: #6B7280;
    font-size: 0.95rem;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 8px;
    color: #1F2937;
}

input {
    width: 100%;
    padding: 12px 15px;
    background-color: #F3F4F6;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    font-size: 0.95rem;
    color: #333333;
    outline: none;
    transition: all 0.2s;
}

input:focus {
    background-color: #FFFFFF;
    border-color: #D1D5DB;
    box-shadow: 0 0 0 3px rgba(0, 176, 80, 0.1);
}

input::placeholder {
    color: #9CA3AF;
}

.boton-submit {
    width: 100%;
    padding: 14px;
    background: linear-gradient(90deg, #4CA626 0%, #009B58 100%);
    color: #FFF;
    font-weight: bold;
    font-size: 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
    transition: background-color 0.2s;
}

.boton-submit:hover {
    background: linear-gradient(90deg, #008F4C 0%, rgb(1, 104, 59) 100%);
}

.boton-submit:disabled {
    background: #cccccc;
    cursor: not-allowed;
    box-shadow: none;
    opacity: 0.7;
    color: #888888
}

.boton-submit:disabled:hover {
    background: #cccccc;
}

.toast-notificacion {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #333;
    color: white;
    padding: 15px 25px;
    border-radius: 8px;
    z-index: 99999;
    animation: subida 0.3s ease-out;
}

@keyframes subida {
    from { transform: translateY(20px); opacity: 0; }
    to   { transform: translateY(0); opacity: 1; }
}

@media (min-width: 1200px) {
    .form-card {
        max-width: 500px;
    }
}

@media (max-width: 768px) {
    .register-container {
        align-items: flex-start;
        padding-top: 40px;
    }

    .form-card {
        width: 100%;
        max-width: none;
        padding: 25px 20px;
    }

    h3 {
        font-size: 1.2rem;
    }
}
</style>