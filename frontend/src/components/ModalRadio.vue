<script setup>
import { ref } from 'vue';

const props = defineProps(['mostrar']);
const emit = defineEmits(['cerrar', 'confirmar']);

const radioTemporal = ref(10);
</script>

<template>
  <div v-if="mostrar" class="modal-overlay">
    <div class="modal-caja">
      <h3>Ajustar proximidad</h3>
      <p>Radio actual: <strong>{{ radioTemporal }} km</strong></p>
      
      <input 
        type="range" 
        v-model="radioTemporal" 
        min="1" 
        max="100"
      >
      
      <div class="botones">
        <button @click="$emit('cerrar')">Cerrar</button>
        <button class="boton-verde" @click="$emit('confirmar', radioTemporal)">Aplicar</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0; left: 0; width: 100vw; height: 100vh;
  background: rgba(0, 0, 0, 0.6);
  display: flex; justify-content: center; align-items: center;
  z-index: 9999;
}

.modal-caja {
  background: white; padding: 30px; border-radius: 15px;
  width: 300px; text-align: center; color: #333;
}

input[type="range"] {
  width: 100%;
  margin: 20px 0;
}

.botones {
  display: flex;
  justify-content: space-around;
}

.boton-verde {
  background: linear-gradient(90deg, #4CA626 0%, #009B58 100%);
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 5px;
  cursor: pointer;
}

.boton-verde:hover {
  background: linear-gradient(90deg, #008F4C 0%, rgb(1, 104, 59) 100%);
}
</style>