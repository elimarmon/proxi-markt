<script setup>
import { reactive } from "vue";

const emit = defineEmits(['enviar-solicitud']);

const props = defineProps({ precio: { type: [Number, String], required: true } })

const form = reactive({
    cantidad: null,
    fecha: null,
    mensaje: '',
})

const enviarSolicitud = async () => {
    emit('enviar-solicitud', {
        cantidad: form.cantidad,
        fecha_prevista: form.fecha,
        mensaje: form.mensaje,
    });

    form.cantidad = null;
    form.fecha = null;
    form.mensaje = "";
};
</script>

<template>
    <form @submit.prevent="enviarSolicitud" class="formulario-compra">
        <h3>Solicitar compra</h3>

        <div class="campo">
            <label for="cantidad">Cantidad</label>
            <input v-model="form.cantidad" type="number" id="cantidad" min="1">
        </div>

        <div class="campo">
            <label for="fecha">Fecha de recogida preferida</label>
            <input v-model="form.fecha" type="date" id="fecha" required>
        </div>

        <div class="campo">
            <label for="mensaje">Mensaje al vendedor (opcional)</label>
            <textarea v-model="form.mensaje" id="mensaje" placeholder="Escribe un mensaje..."></textarea>
        </div>

        <div class="resumen-total" v-if="precio">
            <span>Total:</span>
            <strong>{{ (form.cantidad * precio).toFixed(2) }}€</strong>
        </div>

        <div class="acciones">
            <button type="reset" class="boton-cancelar">Cancelar</button>
            <button type="submit" class="boton-primario">Enviar solicitud</button>
        </div>
    </form>
</template>

<style scoped>
.formulario-compra h3 {
    font-size: 1.1rem;
    color: #333;
    margin-bottom: 20px;
    font-weight: 600;
}

.campo {
    margin-bottom: 15px;
}

label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: #444;
    margin-bottom: 8px;
}

input,
textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #eee;
    border-radius: 8px;
    background-color: #f8f9fa;
    font-size: 0.95rem;
    color: #333;
    outline: none;
    transition: border-color 0.2s;
}

input:focus,
textarea:focus {
    border-color: #4CA626;
    background-color: #fff;
}

textarea {
    height: 80px;
    resize: none;
}

.resumen-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    margin-top: 10px;
    border-top: 1px solid #eee;
}

.resumen-total span {
    color: #888;
}

.resumen-total strong {
    font-size: 1.2rem;
    color: #333;
}

.acciones {
    display: flex;
    gap: 12px;
    margin-top: 10px;
}

.boton-primario {
    flex: 2;
    background: linear-gradient(to right, #5cb82a, #008f4c);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-weight: bold;
    cursor: pointer;
    transition: opacity 0.2s;
}

.boton-primario:hover {
    opacity: 0.9;
}

.boton-cancelar {
    flex: 1;
    background: white;
    color: #666;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 12px;
    font-weight: 600;
    cursor: pointer;
}

.boton-cancelar:hover {
    background: #f5f5f5;
}
</style>