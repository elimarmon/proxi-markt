<script setup>
import axios from 'axios';
import { onMounted } from 'vue';

async function datosUsuario() {
    try {
        const token = localStorage.getItem('token');
        const response = await axios.get("http://localhost:8080/api/datosuser", {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        console.log(response.data)
        return response.data;
    }
    catch (err) {
        console.err(err);
    }
}

onMounted(() => console.log(datosUsuario()));

</script>

<template>
    <div>hola</div>
</template>