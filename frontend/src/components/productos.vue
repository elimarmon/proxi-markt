  <script setup>
  import { ref, onMounted, watch, computed } from "vue";
  import axios from "axios";
  import navbar from "./nav.vue";
  // import TarjetaProducto from "@/components/TarjetaProducto.vue";
  import MostrarProductos from './MostrarProductosMain.vue';

  const productos = ref([]);
  const radioActual = ref(Number(localStorage.getItem('distancia_guardada')) || 10);

  const categoriasSeleccionadas = ref([]);
  const menuAbierto = ref(false);

  const manejarCambioRadio = (nuevoRadio) => {
      radioActual.value = nuevoRadio;
      mostrarProductos();
  };

  const categorias = computed(() => {
    const nombres = productos.value.map(p => p.categoria ? p.categoria.nombre_categoria : null);
    const unicas = [...new Set(nombres)].filter(Boolean);
    return unicas.sort();
  });

  const mostrarProductos = async () => {
      const response = await axios.get("http://localhost:8080/api/productos", {
        params: {
          km: radioActual.value
        }
      });
      productos.value = response.data;
      console.log(response);
  };

  const textoBusqueda = ref("");

  const productosFiltrados = computed(() => {
    return productos.value.filter(p => 
      (!textoBusqueda.value || p.nombre_producto.toLowerCase().includes(textoBusqueda.value.toLowerCase())) &&
      (categoriasSeleccionadas.value.length === 0 || categoriasSeleccionadas.value.includes(p.categoria?.nombre_categoria))
    );
  });

  const toggleMenu = () => {
    menuAbierto.value = !menuAbierto.value;
  }

  onMounted(() => {
      mostrarProductos();
  });
  </script>

  <template>
    <navbar @cambiar-radio="manejarCambioRadio"></navbar>
  
    <div class="contenedor-pagina">
      <div class="zona-fija">
        <h1 class="titulo-verde">Productos Frescos y Locales</h1>
        <p class="subtitulo">Conecta directamente con productores de tu zona (radio: {{ radioActual }} km)</p>

        <div class="card-busqueda">
          <div id="buscador">
            <div class="caja-busqueda">
              <img src="../assets/iconos/buscar.png" alt="lupa" class="icono-pequeno" />
              <input v-model="textoBusqueda" class="input-texto" type="text" placeholder="Buscar productos frescos..."/>
            </div>

            <div class="caja-filtro-especial">
              <button 
                class="boton-secundario" 
                :class="{ 'activo': menuAbierto || categoriasSeleccionadas.length > 0 }"
                @click="toggleMenu"
              >
                <img src="../assets/iconos/filtro.png" alt="filtro" class="icono-pequeno"/>
                <span>
                  {{ categoriasSeleccionadas.length > 0 ? `Filtros (${categoriasSeleccionadas.length})` : 'Filtros' }}
                </span>
              </button>

              <div v-if="menuAbierto" class="menu-checkboxes">
                <label v-for="cat in categorias" :key="cat" class="fila-opcion">
                  <input type="checkbox" :value="cat" v-model="categoriasSeleccionadas">
                  <span class="nombre-cat">{{ cat }}</span>
                </label>
              </div>

            </div> 
          </div> <p class="informacion-resultados"> 
            {{ productosFiltrados.length }} productos encontrados <span class="texto-verde">(en un radio de {{ radioActual }} km)</span>
          </p>
        </div>
      </div> 
      
      <MostrarProductos :productos="productosFiltrados" :radioMaximo="radioActual"></MostrarProductos>
    </div>
  </template>

  <style scoped>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", "Arial";
  }

  body {
    min-width: 400px;
  }

  .contenedor-pagina {
    max-width: 1200px;
    margin: 0 auto;
    padding: 380px 40px 40px;
    font-family: 'Segoe UI', 'Arial';
  }

  .titulo-verde { 
    color: #4CA626; 
    font-size: 2rem; 
    margin-bottom: 5px; 
    font-weight: bold;
  }

  .subtitulo { 
    color: #666666; 
    margin-bottom: 30px; 
  }

  .zona-fija {
    position: fixed;
    top: 80px;
    left: 0;
    width: 100%;
    height: auto;
    background-color: white;
    z-index: 900;
    box-shadow: 0 10px 20px -10px rgba(255, 255, 255, 1);
    padding-top: 20px;
    padding-left: max(40px, calc((100% - 1200px) / 2 + 40px));
    padding-right: max(40px, calc((100% - 1200px) / 2 + 40px));
    box-sizing: border-box;
  }

  .card-busqueda {
    background: white;
    border: 1px solid #EEEEEE;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  }

  #buscador {
    display: flex; 
    align-items: center; 
    gap: 15px; 
    margin-bottom: 10px;
  }

  .caja-busqueda {
    background-color: #F9F9F9;
    border-radius: 8px;
    padding: 12px 15px;
    display: flex; 
    align-items: center; 
    flex-grow: 1; 
    border: 1px solid #DDDDDD;
  }

  .input-texto {
    border: none; 
    background: transparent; 
    width: 100%; 
    margin-left: 10px;
    outline: none; 
    font-size: 16px; 
    color: #333333;
  }

  .icono-pequeno { 
    width: 20px; 
    height: 20px;
    
  }

  .informacion-resultados {
    font-size: 0.9rem; 
    color: #666666;
    padding-left: 5px;
  }

  .texto-verde {
    color: #4CA626; 
    }

  .caja-filtro-especial {
    position: relative;
  }

  .boton-secundario {
    background-color: white;
    border: 1px solid #DDDDDD;
    border-radius: 8px;
    padding: 12px 20px;
    cursor: pointer;
    display: flex; 
    align-items: center; 
    gap: 10px;
    font-weight: 600; 
    color: #333;
    transition: all 0.2s ease;
    min-width: 140px;
    justify-content: space-between;
  }

  .boton-secundario:hover {
    background-color: #f8f8f8;
    border-color: #ccc;
  }

  .boton-secundario.activo {
    border-color: #4CA626;
    background-color: #f0fdf4;
    color: #4CA626;
  }

  .flecha {
    font-size: 10px;
    color: #999;
    transition: transform 0.2s ease;
  }

  .flecha.rotada {
    transform: rotate(180deg);
  }

  .menu-checkboxes {
    position: absolute;
    top: calc(100% + 8px); 
    right: 0;
    width: 260px; 
    background: white;
    border: 1px solid #eee;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.12); 
    z-index: 1000;
    overflow: hidden;
    animation: fadeIn 0.2s ease-out;
  }

  .menu-header {
    padding: 12px 16px;
    background: #fcfcfc;
    border-bottom: 1px solid #eee;
    font-size: 13px;
    font-weight: 600;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .lista-opciones {
    padding: 8px;
    max-height: 300px; 
    overflow-y: auto;
  }

  .fila-opcion {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.2s;
    user-select: none;
  }

  .fila-opcion:hover {
    background-color: #f0fdf4; 
  }

  .nombre-cat {
    font-size: 15px;
    color: #333;
  }

  .fila-opcion input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    width: 18px;
    height: 18px;
    border: 2px solid #ccc;
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
    position: relative;
    display: flex; align-items: center; justify-content: center;
  }

  .fila-opcion input[type="checkbox"]:checked {
    background-color: #4CA626;
    border-color: #4CA626;
  }

  .fila-opcion input[type="checkbox"]:checked::after {
    content: '✔';
    font-size: 12px;
    color: white;
    font-weight: bold;
  }

  .menu-footer {
    padding: 10px;
    border-top: 1px solid #eee;
    text-align: right;
  }

  .limpiar-texto {
    font-size: 13px;
    color: #e53935;
    cursor: pointer;
    font-weight: 500;
  }

  .limpiar-texto:hover {
    text-decoration: underline;
  }

  @keyframes fadeIn {
    from { opacity: 0; 
      transform: translateY(-10px); 
    }
    to { opacity: 1; 
      transform: translateY(0); 
    }
  }

  @media (max-width: 768px) {
    .contenedor-pagina { padding: 0 20px 20px 20px; }
    #buscador { flex-direction: column; align-items: stretch; }
    .menu-checkboxes { width: 100%; } 
  }

  </style>
