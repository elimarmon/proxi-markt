<script setup>
    import { ref, onMounted } from 'vue';
    import axios from 'axios';
    import Navbar from './nav.vue'
    import { useRouter } from 'vue-router';

    const router = useRouter()

    const PuntosEntrega = ref([])
    const Categorias = ref([])


    const nombre_producto = ref('');
    const descripcion = ref('');
    const precio = ref(0);
    const stock = ref(0);
    const puntoentrega = ref(''); 
    const categoria = ref('');     
    const imagen = ref(null);

    const GuardarImagen = (event) => {
        imagen.value = event.target.files[0];
    }

    const InsertarProducto = async () => {
    const token = localStorage.getItem('token');
    try {
        const datos = new FormData();
        datos.append('nombre_producto', nombre_producto.value);
        datos.append('descripcion', descripcion.value);
        datos.append('precio', precio.value);
        datos.append('stock_total', stock.value); 
        datos.append('id_categoria', categoria.value);
        datos.append('id_puntoentrega', puntoentrega.value);

        if (imagen.value) {
            datos.append('imagen', imagen.value);
        }

        const respuesta = await axios.post('http://localhost:8080/api/publicarproducto', datos, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'multipart/form-data',
                'Accept': 'application/json'
            }
        });
    
        if (respuesta.status === 201 || respuesta.status === 200) {
            alert('¡Producto creado correctamente!');
            router.push('/cuenta');
        }
    } catch (error) {
        console.error("Error:", error.response?.data);
        alert('Error: ' + (error.response?.data?.message || 'No se pudo crear'));
    }
}

    const CargarPuntos = async() => {
        const token = localStorage.getItem('token');
        const resposta = await axios.get('http://localhost:8080/api/puntosuser', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        PuntosEntrega.value = resposta.data;
    }

    const CargarCategorias = async() => {
        const resposta = await axios.get('http://localhost:8080/api/categorias');
        Categorias.value = resposta.data;
    }

    onMounted(() => {
      CargarPuntos();
      CargarCategorias();
    });
</script>

<template>
  <Navbar></Navbar>
  <div class="contenedor-pagina">
    <div class="encabezado-seccion">
      <h1 class="titulo-principal">Publicar Producto</h1>
      <p class="subtitulo">Comparte tus frutas y verduras frescas con la comunidad</p>
    </div>

    <div class="tarjeta-formulario">
      <h3 class="header-interno">Publicar nuevo producto</h3>
      
      <form @submit.prevent="InsertarProducto">
        <div class="campo">
          <label>Nombre del producto </label>
          <input v-model="nombre_producto" type="text" placeholder="Ej: Tomates orgánicos" required>
        </div>

        <div class="campo">
          <label>Descripción </label>
          <textarea v-model="descripcion" placeholder="Describe tu producto en detalle..." required></textarea>
        </div>

        <div class="fila-doble">
          <div class="columna">
            <label>Precio (€) </label>
            <input v-model="precio" type="number" step="0.01" placeholder="0.00" required>
          </div>
          <div class="columna">
            <label>Stock disponible </label>
            <input v-model="stock" type="number" placeholder="Cantidad" required>
          </div>
        </div>

        <div class="campo">
          <label>Categoría </label>
          <select v-model="categoria" required>
            <option value="" disabled>Selecciona una categoría</option>
            <option v-for="cat in Categorias" :key="cat.id" :value="cat.id">
                {{ cat.nombre_categoria }}
            </option>
          </select>
        </div>

        <div class="campo">
          <label>Punto de entrega </label>
          <select v-if="PuntosEntrega.length > 0" v-model="puntoentrega" required>
            <option value="" disabled>Ej: Mercado de Santa Caterina</option>
            <option v-for="punto in PuntosEntrega" :key="punto.id" :value="punto.id">
                {{ punto.nombre_punto }}
            </option>
          </select>
          <p class="ayuda-texto" v-if="PuntosEntrega.length > 0">Indica dónde el comprador podrá recoger el producto</p>
          <p v-else class="error-texto">* Debes crear al menos un punto de entrega en tu perfil.</p>
        </div>

        <div class="campo">
          <label>Imagen del producto (opcional)</label>
          <div class="zona-upload">
            <input type="file" @change="GuardarImagen" accept="image/*" class="input-file-oculto">
            <div class="diseno-upload">
              <span class="icono-nube">↑</span>
              <p>Haz clic para subir o arrastra una imagen</p>
              <small>PNG, JPG o WEBP (máx. 5MB)</small>
            </div>
          </div>
        </div>

        <div class="banner-informativo">
          <p><strong>Nota:</strong> Una vez publicado, los compradores podrán ver tu producto y enviarte solicitudes de compra.</p>
        </div>

        <div class="acciones">
          <button type="button" class="btn-cancelar" @click="$router.go(-1)">Cancelar</button>
          <button type="submit" class="btn-publicar" :disabled="PuntosEntrega.length === 0">Publicar producto</button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');


* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  min-width: 400px;
}

.contenedor-pagina {
  background-color: #fcfcfc;
  min-height: 100vh;
  padding: 100px 20px 60px;
  font-family: 'Inter', sans-serif;
  display: flex;
  flex-direction: column;
  align-items: center; 
}


.encabezado-seccion {
  width: 100%;
  max-width: 650px; 
  margin-bottom: 25px;
  text-align: left; 
}

.titulo-principal {
  font-family: sans-serif;
  color: #4ca626;
  margin-bottom: 10px;
  font-weight: bold;
}

.subtitulo {
  color: #6b7280;
  font-size: 14px;
}


.tarjeta-formulario {
  background: white;
  width: 100%;
  max-width: 650px;
  padding: 35px;
  border-radius: 12px;
  border: 1px solid #edf2f7;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
  box-sizing: border-box;
}

.header-interno {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 25px;
  color: #1a202c;
}

label {
  display: block;
  font-size: 13.5px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 8px;
}


input[type="text"], input[type="number"], select, textarea {
  width: 100%;
  padding: 11px 14px;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 14px;
  margin-bottom: 18px;
  transition: all 0.2s ease;
  font-family: inherit;
  box-sizing: border-box;
}

input:focus, select:focus, textarea:focus {
  outline: none;
  border-color: #00a859;
  box-shadow: 0 0 0 3px rgba(0, 168, 89, 0.1);
  background-color: #fff;
}


.fila-doble {
  display: flex;
  gap: 60px; 
  width: 100%;
}

.columna { flex: 1; }

.ayuda-texto {
  font-size: 12px;
  color: #a0aec0;
  margin-top: -12px;
  margin-bottom: 15px;
}

.error-texto {
  font-size: 12px;
  color: #e53e3e;
  margin-top: 10px;
  margin-bottom: 20px;
}

.zona-upload {
  position: relative;
  border: 2px dashed #d1d5db;
  border-radius: 10px;
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  margin-bottom: 25px;
  background-color: #fff;
  transition: all 0.2s;
}

.zona-upload:hover {
    background-color: #f9fafb;
    border-color: #00a859;
}

.input-file-oculto {
  position: absolute;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.diseno-upload {
  text-align: center;
  color: #718096;
}

.icono-nube { font-size: 32px; display: block; margin-bottom: 8px; color: #9ca3af; }

.diseno-upload p { font-size: 14px; font-weight: 600; margin-bottom: 4px; color: #4b5563; }

.diseno-upload small { font-size: 11px; color: #9ca3af; }

/* NOTA AZUL */
.banner-informativo {
  background-color: #eff6ff; /* Fondo azul claro */
  border: 1px solid #dbeafe; /* Borde azul suave */
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 30px;
}

.banner-informativo p {
  color: #1e40af; /* Texto azul oscuro */
  font-size: 13px;
  line-height: 1.5;
}

.banner-informativo strong {
    color: #1e3a8a;
}

.acciones {
  display: flex;
  gap: 20px;
  width: 100%;
}

button {
  flex: 1;
  padding: 14px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancelar {
  background: white;
  border: 1px solid #d1d5db;
  color: #374151;
}

.btn-cancelar:hover {
    background-color: #f9fafb;
}

/* BOTÓN CON DEGRADADO VERDE */
.btn-publicar {
  background: linear-gradient(135deg, #00b050 0%, #00d660 100%);
  border: none;
  color: white;
  box-shadow: 0 4px 6px rgba(0, 176, 80, 0.2);
}

.btn-publicar:hover { 
    background: linear-gradient(135deg, #009945 0%, #00c256 100%);
    box-shadow: 0 6px 8px rgba(0, 176, 80, 0.3);
}

.btn-publicar:disabled { 
    background: #cbd5e0; 
    cursor: not-allowed;
    box-shadow: none;
}
</style>