<template>
    <p>PAGINA PRICIPAL</p>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import navbar from "./nav.vue";


const router = useRouter();
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', 'Arial';
}

body {
  min-width: 400px;
}
</style>