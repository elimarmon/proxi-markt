<template>
  <div class="main-wrapper">
    <navbar />

    <section class="hero-section">
      <div class="hero-content">
        <h1>Conectamos Productores Locales <br> <span class="text-green">con Consumidores Conscientes</span></h1>
        <p class="hero-text">
          ProxyMarket es el marketplace líder en productos frescos de proximidad. Una solución profesional para la compra-venta de frutas y verduras de kilómetro cero, con sistema de geolocalización avanzado y gestión integral de pedidos.
        </p>
        <button class="btn-primary" @click="irARegistro">Comenzar Ahora</button>
      </div>
    </section>

    <section class="features-section">
      <h2>¿Por qué elegir <span class="text-green">ProxyMarket?</span></h2>
      <p class="section-subtitle">Tecnología avanzada al servicio del comercio local de productos frescos</p>

      <div class="features-grid">
        <div class="feature-card">
          <div class="icon-box green-icon">📍</div>
          <h3>Sistema de Proximidad</h3>
          <p>Algoritmo de geolocalización que permite búsquedas personalizadas con radio configurable de 1 a 50 kilómetros. Optimiza tus compras encontrando productos en tu zona.</p>
        </div>

        <div class="feature-card">
          <div class="icon-box blue-icon">🍎</div>
          <h3>Trazabilidad Completa</h3>
          <p>Productos verificados de origen local con información detallada del productor. Garantía de frescura, calidad y sostenibilidad en cada transacción.</p>
        </div>

        <div class="feature-card">
          <div class="icon-box purple-icon">👥</div>
          <h3>Gestión Profesional</h3>
          <p>Plataforma integral con sistema de mensajería, gestión de pedidos, valoraciones y panel de control para vendedores y compradores.</p>
        </div>
      </div>
    </section>

    <section class="how-it-works-section">
      <h2>Cómo Funciona</h2>
      <p class="section-subtitle">Simple, rápido y directo. Así de fácil es usar ProxyMarket</p>

      <div class="steps-grid">
        <div class="step">
          <div class="step-circle green-circle">1</div>
          <h3>Regístrate Gratis</h3>
          <p>Crea tu cuenta en segundos y configura tu radio de búsqueda preferido.</p>
        </div>
        <div class="step">
          <div class="step-circle blue-circle">2</div>
          <h3>Explora Productos</h3>
          <p>Navega por el catálogo de productos frescos disponibles cerca de ti.</p>
        </div>
        <div class="step">
          <div class="step-circle orange-circle">3</div>
          <h3>Conecta y Compra</h3>
          <p>Chatea con el vendedor, acuerda la entrega y disfruta de productos frescos.</p>
        </div>
      </div>
    </section>

    <section class="support-local-section">
      <div class="support-content">
        <div class="badge">🌱 Sostenibilidad</div>
        <h2>Apoya el <span class="text-green">Comercio Local</span></h2>
        <p>Al comprar en ProxyMarket, estás apoyando directamente a productores locales, reduciendo la huella de carbono y asegurando que tu dinero se quede en tu comunidad.</p>
        
        <div class="check-item">
          <span class="check-icon">✓</span>
          <div>
            <strong>Productos Frescos</strong>
            <p>Directamente del huerto a tu mesa, máxima frescura garantizada.</p>
          </div>
        </div>
      </div>
      
      <div class="support-image">
        <img src="../assets/iconos/Mercado.jpeg" alt="Verduras frescassss">
      </div>
    </section>

    <section class="cta-section">
      <h2>¿Listo para Comenzar?</h2>
      <p>Únete a ProxyMarket hoy y descubre la mejor manera de comprar y vender productos frescos locales</p>
      <button class="btn-white" @click="irARegistro">Crear Cuenta Gratis</button>
    </section>

    <footer class="main-footer">
      <div class="footer-bottom">
        <div class="brand">
          <span class="leaf-icon">🍃</span> ProxyMarket
        </div>
        <p class="copyright">Frutas y Verduras Frescas de tu Vecindario</p>
        <p class="copyright small">© 2026 ProxyMarket. Todos los derechos reservados.</p>
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
/* =========================================
   RESET Y FUENTES
   ========================================= */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', Arial, sans-serif;
}

body {
  min-width: 400px;
}

.main-wrapper {
  margin-top: 80px; 
}

/* Colores de texto */
.text-green {
  color: #00a859;
}

.section-subtitle {
  color: #666;
  margin-bottom: 40px;
  font-size: 16px;
}

/* =========================================
   1. HERO SECTION
   ========================================= */
.hero-section {
  text-align: center;
  padding: 80px 20px;
  background-color: #f8fbfa;
}

.hero-content {
  max-width: 800px;
  margin: 0 auto;
}

.hero-section h1 {
  font-size: 42px;
  font-weight: 700;
  color: #1a202c;
  line-height: 1.2;
  margin-bottom: 20px;
}

.hero-text {
  font-size: 18px;
  color: #4a5568;
  margin-bottom: 30px;
  line-height: 1.6;
}

.btn-primary {
  background-color: #00a859;
  color: white;
  border: none;
  padding: 12px 30px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-primary:hover {
  background-color: #008f4c;
}

/* =========================================
   2. FEATURES SECTION
   ========================================= */
.features-section {
  padding: 60px 20px;
  text-align: center;
  background: white;
}

.features-section h2 {
  font-size: 32px;
  margin-bottom: 10px;
}

.features-grid {
  display: flex;
  justify-content: center;
  gap: 30px;
  flex-wrap: wrap;
  max-width: 1200px;
  margin: 0 auto;
}

.feature-card {
  flex: 1;
  min-width: 300px;
  padding: 30px;
  border-radius: 12px;
  border: 1px solid #eee;
  text-align: left;
  transition: transform 0.2s;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.05);
}

.icon-box {
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  font-size: 24px;
  margin-bottom: 20px;
}

.green-icon { background-color: #e6f7ef; color: #00a859; }
.blue-icon { background-color: #e6f0ff; color: #0066ff; }
.purple-icon { background-color: #f3e6ff; color: #8000ff; }

.feature-card h3 {
  font-size: 18px;
  margin-bottom: 10px;
  color: #2d3748;
}

.feature-card p {
  font-size: 14px;
  color: #718096;
  line-height: 1.5;
}

/* =========================================
   3. HOW IT WORKS
   ========================================= */
.how-it-works-section {
  padding: 60px 20px;
  text-align: center;
  background-color: #fff;
}

.how-it-works-section h2 {
  font-size: 32px;
  color: #1a202c;
  margin-bottom: 10px;
}

.steps-grid {
  display: flex;
  justify-content: center;
  gap: 50px;
  flex-wrap: wrap;
  max-width: 1000px;
  margin: 40px auto 0;
}

.step {
  flex: 1;
  min-width: 250px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.step-circle {
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

.green-circle { background-color: #e6f7ef; color: #00a859; }
.blue-circle { background-color: #e6f0ff; color: #0066ff; }
.orange-circle { background-color: #fff4e6; color: #ff9900; }

.step h3 {
  font-size: 18px;
  margin-bottom: 10px;
}

.step p {
  font-size: 14px;
  color: #718096;
  max-width: 250px;
}

/* =========================================
   4. SUPPORT LOCAL
   ========================================= */
.support-local-section {
  display: flex;
  flex-wrap: wrap;
  max-width: 1100px;
  margin: 80px auto;
  padding: 0 20px;
  gap: 40px;
  align-items: center;
}

.support-content {
  flex: 1;
  min-width: 300px;
}

.support-image {
  flex: 1;
  min-width: 300px;
}

.support-image img {
  width: 100%;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.badge {
  display: inline-block;
  background-color: #e6f7ef;
  color: #00a859;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 15px;
}

.support-content h2 {
  font-size: 32px;
  margin-bottom: 20px;
  line-height: 1.2;
}

.support-content p {
  color: #718096;
  line-height: 1.6;
  margin-bottom: 20px;
}

.check-item {
  display: flex;
  gap: 15px;
  margin-top: 20px;
}

.check-icon {
  width: 24px;
  height: 24px;
  background-color: #e6f7ef;
  color: #00a859;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
}

.check-item strong {
  display: block;
  color: #2d3748;
  margin-bottom: 2px;
}

.check-item p {
  font-size: 13px;
  margin: 0;
}

/* =========================================
   5. CTA SECTION (SOLO ESTO VERDE)
   ========================================= */
.cta-section {
  background-color: #00a859; /* Fondo Verde */
  color: white;              /* Texto Blanco */
  text-align: center;
  padding: 80px 20px;
}

.cta-section h2 {
  font-size: 32px;
  margin-bottom: 15px;
}

.cta-section p {
  font-size: 16px;
  opacity: 0.9;
  margin-bottom: 30px;
}

.btn-white {
  background-color: white;
  color: #00a859; /* Texto verde */
  border: none;
  padding: 12px 30px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
  transition: transform 0.2s;
}

.btn-white:hover {
  transform: scale(1.05);
}

/* =========================================
   6. FOOTER REAL (FONDO BLANCO)
   ========================================= */
.main-footer {
  background-color: #ffffff; /* Blanco */
  padding: 40px 20px;
  text-align: center;
}

.footer-bottom {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.brand {
  font-size: 20px;
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
  color: #00a859; /* Logo en verde */
}

.leaf-icon {
    font-size: 24px;
}

.copyright {
  font-size: 14px;
  color: #333; /* Texto oscuro */
  margin-bottom: 5px;
}

.copyright.small {
  font-size: 12px;
  color: #888; /* Texto gris suave */
}
</style>