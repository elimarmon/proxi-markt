<template>
  <div class="contenedor-principal">
    <navbar />

    <section class="seccion-portada">
      <div class="contenido-portada">
        <h1>Conectamos Productores Locales <br> <span class="texto-verde">con Consumidores Conscientes</span></h1>
        <p class="texto-portada">
          ProxyMarket es el marketplace líder en productos frescos de proximidad. Una solución profesional para la compra-venta de frutas y verduras de kilómetro cero, con sistema de geolocalización avanzado y gestión integral de pedidos.
        </p>
        <button class="boton-primario" @click="irARegistro">Comenzar Ahora</button>
      </div>
    </section>

    <section class="seccion-caracteristicas">
      <h2>¿Por qué elegir <span class="texto-verde">ProxyMarket?</span></h2>
      <p class="subtitulo-seccion">Tecnología avanzada al servicio del comercio local de productos frescos</p>

      <div class="grid-caracteristicas">
        <div class="tarjeta-caracteristica">
          <div class="caja-icono icono-verde">📍</div>
          <h3>Sistema de Proximidad</h3>
          <p>Algoritmo de geolocalización que permite búsquedas personalizadas con radio configurable de 1 a 50 kilómetros. Optimiza tus compras encontrando productos en tu zona.</p>
        </div>

        <div class="tarjeta-caracteristica">
          <div class="caja-icono icono-azul">🍎</div>
          <h3>Trazabilidad Completa</h3>
          <p>Productos verificados de origen local con información detallada del productor. Garantía de frescura, calidad y sostenibilidad en cada transacción.</p>
        </div>

        <div class="tarjeta-caracteristica">
          <div class="caja-icono icono-morado">👥</div>
          <h3>Gestión Profesional</h3>
          <p>Plataforma integral con sistema de mensajería, gestión de pedidos, valoraciones y panel de control para vendedores y compradores.</p>
        </div>
      </div>
    </section>

    <section class="seccion-como-funciona">
      <h2>Cómo Funciona</h2>
      <p class="subtitulo-seccion">Simple, rápido y directo. Así de fácil es usar ProxyMarket</p>

      <div class="grid-pasos">
        <div class="paso">
          <div class="circulo-paso circulo-verde">1</div>
          <h3>Regístrate Gratis</h3>
          <p>Crea tu cuenta en segundos y configura tu radio de búsqueda preferido.</p>
        </div>
        <div class="paso">
          <div class="circulo-paso circulo-azul">2</div>
          <h3>Explora Productos</h3>
          <p>Navega por el catálogo de productos frescos disponibles cerca de ti.</p>
        </div>
        <div class="paso">
          <div class="circulo-paso circulo-naranja">3</div>
          <h3>Conecta y Compra</h3>
          <p>Chatea con el vendedor, acuerda la entrega y disfruta de productos frescos.</p>
        </div>
      </div>
    </section>

    <section class="seccion-apoyo-local">
      <div class="contenido-apoyo">
        <div class="insignia">🌱 Sostenibilidad</div>
        <h2>Apoya el <span class="texto-verde">Comercio Local</span></h2>
        <p>Al comprar en ProxyMarket, estás apoyando directamente a productores locales, reduciendo la huella de carbono y asegurando que tu dinero se quede en tu comunidad.</p>
        
        <div class="elemento-check">
          <span class="icono-check">✓</span>
          <div>
            <strong>Productos Frescos</strong>
            <p>Directamente del huerto a tu mesa, máxima frescura garantizada.</p>
          </div>
        </div>
      </div>
      
      <div class="imagen-apoyo">
        <img src="../assets/iconos/Mercado.jpeg" alt="Verduras frescas">
      </div>
    </section>

    <section class="seccion-llamada">
      <h2>¿Listo para Comenzar?</h2>
      <p>Únete a ProxyMarket hoy y descubre la mejor manera de comprar y vender productos frescos locales</p>
      <button class="boton-blanco" @click="irARegistro">Crear Cuenta Gratis</button>
    </section>

    <footer class="pie-pagina-principal">
      <div class="fondo-pie">
        <div class="marca">
          <span class="icono-hoja">🍃</span> ProxyMarket
        </div>
        <p class="derechos">Frutas y Verduras Frescas de tu Vecindario</p>
        <p class="derechos pequeno">© 2026 ProxyMarket. Todos los derechos reservados.</p>
      </div>
    </footer>

  </div>
</template>

<script setup>
import { useRouter } from "vue-router";
import navbar from "./nav.vue"; 

const router = useRouter();

const irARegistro = () => {
  router.push('/login'); 
};
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

.contenedor-principal {
  margin-top: 80px; 
}


.texto-verde {
  color: #00A859;
}

.subtitulo-seccion {
  color: #666666;
  margin-bottom: 40px;
  font-size: 16px;
}

.seccion-portada {
  text-align: center;
  padding: 80px 20px;
  background-color: #F8FBFA;
}

.contenido-portada {
  max-width: 800px;
  margin: 0 auto;
}

.seccion-portada h1 {
  font-size: 42px;
  font-weight: bold;
  color: #1A202C;
  line-height: 1.2;
  margin-bottom: 20px;
}

.texto-portada {
  font-size: 18px;
  color: #4A5568;
  margin-bottom: 30px;
  line-height: 1.6;
}

.boton-primario {
  background-color: #00A859;
  color: white;
  border: none;
  padding: 12px 30px;
  font-size: 16px;
  font-weight: bold;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s;
}

.boton-primario:hover {
  background-color: #008F4C;
}

.seccion-caracteristicas {
  padding: 60px 20px;
  text-align: center;
  background: white;
}

.seccion-caracteristicas h2 {
  font-size: 32px;
  margin-bottom: 10px;
}

.grid-caracteristicas {
  display: flex;
  justify-content: center;
  gap: 30px;
  flex-wrap: wrap;
  max-width: 1200px;
  margin: 0 auto;
}

.tarjeta-caracteristica {
  flex: 1;
  min-width: 300px;
  padding: 30px;
  border-radius: 12px;
  border: 1px solid #EEEEEE;
  text-align: left;
  transition: transform 0.2s;
}

.tarjeta-caracteristica:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.05);
}

.caja-icono {
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  font-size: 24px;
  margin-bottom: 20px;
}

.icono-verde {
  background-color: #E6F7EF; 
  color: #00A859;
}

.icono-azul {
  background-color: #E6F0FF; 
  color: #0066FF;
}

.icono-morado {
  background-color: #F3E6FF; 
  color: #8000FF;
}


.tarjeta-caracteristica h3 {
  font-size: 18px;
  margin-bottom: 10px;
  color: #2D3748;
}

.tarjeta-caracteristica p {
  font-size: 14px;
  color: #718096;
  line-height: 1.5;
}

.seccion-como-funciona {
  padding: 60px 20px;
  text-align: center;
  background-color: #FFFFFF;
}

.seccion-como-funciona h2 {
  font-size: 32px;
  color: #1A202C;
  margin-bottom: 10px;
}

.grid-pasos {
  display: flex;
  justify-content: center;
  gap: 50px;
  flex-wrap: wrap;
  max-width: 1000px;
  margin: 40px auto 0;
}

.paso {
  flex: 1;
  min-width: 250px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.circulo-paso {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 20px;
}

.circulo-verde {
  background-color: #E6F7EF;
  color: #00A859;
}

.circulo-azul {
  background-color: #E6F0FF;
  color: #0066FF;
}

.circulo-naranja { 
  background-color: #FFF4E6; 
  color: #FF9900;
}

.paso h3 {
  font-size: 18px;
  margin-bottom: 10px;
}

.paso p {
  font-size: 14px;
  color: #718096;
  max-width: 250px;
}

.seccion-apoyo-local {
  display: flex;
  flex-wrap: wrap;
  max-width: 1100px;
  margin: 80px auto;
  padding: 0 20px;
  gap: 40px;
  align-items: center;
}

.contenido-apoyo {
  flex: 1;
  min-width: 300px;
}

.imagen-apoyo {
  flex: 1;
  min-width: 300px;
}

.imagen-apoyo img {
  width: 100%;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.insignia {
  display: inline-block;
  background-color: #E6F7EF;
  color: #00A859;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 15px;
}

.contenido-apoyo h2 {
  font-size: 32px;
  margin-bottom: 20px;
  line-height: 1.2;
}

.contenido-apoyo p {
  color: #718096;
  line-height: 1.6;
  margin-bottom: 20px;
}

.elemento-check {
  display: flex;
  gap: 15px;
  margin-top: 20px;
}

.icono-check {
  width: 24px;
  height: 24px;
  background-color: #E6F7EF;
  color: #00A859;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
}

.elemento-check strong {
  display: block;
  color: #2D3748;
  margin-bottom: 2px;
}

.elemento-check p {
  font-size: 13px;
  margin: 0;
}

.seccion-llamada {
  background-color: #00A859; 
  color: white;              
  text-align: center;
  padding: 80px 20px;
}

.seccion-llamada h2 {
  font-size: 32px;
  margin-bottom: 15px;
}

.seccion-llamada p {
  font-size: 16px;
  opacity: 0.9;
  margin-bottom: 30px;
}

.boton-blanco {
  background-color: white;
  color: #00A859; 
  border: none;
  padding: 12px 30px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
  transition: transform 0.2s;
}

.boton-blanco:hover {
  transform: scale(1.05);
}

.pie-pagina-principal {
  background-color: #FFFFFF; 
  padding: 40px 20px;
  text-align: center;
}

.fondo-pie {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.marca {
  font-size: 20px;
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
  color: #00A859; 
}

.icono-hoja {
    font-size: 24px;
}

.derechos {
  font-size: 14px;
  color: #333333; 
  margin-bottom: 5px;
}

.derechos.pequeno {
  font-size: 12px;
  color: #888888; 
}

</style>