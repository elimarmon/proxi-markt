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
            <input v-model="form.cantidad" type="number" id="cantidad" min="1" placeholder="Ej: 10">
        </div>

        <div class="campo">
            <label for="fecha">Fecha de recogida preferida</label>
            <input v-model="form.fecha" type="date" id="fecha" required>
        </div>

        <div class="campo">
            <label for="mensaje">Mensaje al vendedor (opcional)</label>
            <textarea v-model="form.mensaje" id="mensaje" placeholder="Escribe un mensaje para el productor..."></textarea>
        </div>

        <div class="resumen-total" v-if="precio && form.cantidad">
            <span>Total estimado:</span>
            <strong>{{ (form.cantidad * precio).toFixed(2) }}€</strong>
        </div>

        <div class="acciones">
            <button type="reset" class="boton-cancelar">Limpiar</button>
            <button type="submit" class="boton-primario">Enviar solicitud</button>
        </div>
    </form>
</template>

<style scoped>
.formulario-compra{
    padding-right: 20px;
}

.formulario-compra h3 {
    font-size: 1.15rem;
    color: #333;
    margin-bottom: 24px; /* Mayor separación del título */
    font-weight: 700;
}

.campo {
    margin-bottom: 20px; /* Aumentado de 15px a 20px para mayor aire entre campos */
}

label {
    display: block;
    font-size: 0.8rem;
    font-weight: 700;
    color: #666; /* Un tono un poco más suave para los labels */
    margin-bottom: 6px; /* Reducido ligeramente para pegar más el label a su input */
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

input,
textarea {
    width: 100%;
    padding: 12px 14px; /* Un poco más de padding lateral */
    border: 1px solid #e2e8f0; /* Borde un poco más definido pero suave */
    border-radius: 8px;
    background-color: #fcfcfc;
    font-size: 0.95rem;
    color: #333;
    outline: none;
    transition: all 0.2s ease-in-out;
    /* Corrección del margen derecho: asegura que el padding no desborde el 100% de ancho */
    box-sizing: border-box;
}

input:focus,
textarea:focus {
    border-color: #4CA626;
    background-color: #fff;
    box-shadow: 0 0 0 3px rgba(76, 166, 38, 0.1); /* Efecto de foco sutil */
}

textarea {
    height: 90px;
    resize: none;
    line-height: 1.5;
}

.resumen-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 0;
    margin-top: 5px;
    border-top: 1px solid #f0f0f0;
}

.resumen-total span {
    color: #777;
    font-size: 0.9rem;
}

.resumen-total strong {
    font-size: 1.4rem;
    color: #4CA626; /* El total resalta en el color corporativo */
}

.acciones {
    display: flex;
    gap: 12px;
    margin-top: 15px; /* Separación respecto al total */
}

.boton-primario {
    flex: 2;
    background: linear-gradient(to right, #5cb82a, #008f4c);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 14px;
    font-weight: bold;
    font-size: 1rem;
    cursor: pointer;
    transition: transform 0.1s, opacity 0.2s;
}

.boton-primario:hover {
    opacity: 0.95;
    transform: translateY(-1px);
}

.boton-primario:active {
    transform: translateY(0);
}

.boton-cancelar {
    flex: 1;
    background: white;
    color: #888;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.boton-cancelar:hover {
    background: #f9f9f9;
    color: #666;
}
</style>