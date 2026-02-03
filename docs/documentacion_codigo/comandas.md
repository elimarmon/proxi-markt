# Comandas.vue

```vue
<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import navbar from "./nav.vue";

const comandas = ref([]);
const cargando = ref(true);
const comprador = ref(false);
const token = localStorage.getItem("token");

const obtenerComandas = async () => {
  try {
    const response = await axios.get("http://localhost:8080/api/miscomandas", {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
        "Content-Type": "application/json",
      },
    });

    comprador.value = response.data.comprador;
    comandas.value = response.data.datos;
    console.log(comandas.value);
  } catch (error) {
    console.error("Error:", error);
  } finally {
    cargando.value = false;
  }
};

onMounted(() => {
  obtenerComandas();
});

const getUrlImagen = (rutaRelativa) => {
  if (!rutaRelativa) {
    return "https://via.placeholder.com/150";
  }
  return `http://localhost:808/storage/${rutaRelativa}`;
};
</script>
```
