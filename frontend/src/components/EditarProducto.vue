<script setup>
    import { reactive, onMounted } from 'vue';
    import axios from 'axios';
    import navbar from './nav.vue'
    import { useRouter } from 'vue-router';

    const router = useRouter()


    const props = defineProps({
        id: {
            type: [String, Number],
            required: true
        }
    });

    const formulario = reactive({
        id: props.id,
        nombre_producto: "",
        descripcion: "",
        precio: 0,
        stock_total: 0,
    })

    const CargarProducto = async () => {
        const producto = await axios.get('http://localhost:8080/api/productos/'+props.id)
        const datosproducto = producto.data;

        Object.assign(formulario, datosproducto)

    }

    const emits = defineEmits(['editar']);

    const editarProducto = async () => {
        const token = localStorage.getItem('token');

        const editar = await axios.put('http://localhost:8080/api/productos/'+props.id, formulario, {
            headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        })

        if(editar.status === 200){
            router.push('/cuenta')
        }

    }

    onMounted(() => {
        CargarProducto();
    })

</script>
<template>
    <navbar></navbar>
    <form @submit.prevent="editarProducto">
        <label for="nombre">Nombre producto</label><br>
        <input v-model="formulario.nombre_producto" type="text" name="nombre" id="nombre"><br><br>

        <label for="descripcion">Descripcion del producto</label><br>
        <input v-model="formulario.descripcion" type="text" name="descripcion" id="descripcion"><br><br>

        <label for="precio">Precio del producto</label><br>
        <input v-model="formulario.precio" type="number" name="precio" id="precio"><br><br>

        <label for="stock">Stock del producto</label><br>
        <input v-model="formulario.stock_total" type="number" name="stock" id="stock"><br><br>

        <button type="submit">Actualizar</button>
    </form>
</template>

<style scoped>
    form{
        margin: 100px;
    }
</style>