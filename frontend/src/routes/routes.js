import login from "../views/login.vue";
import { createRouter, createWebHistory } from "vue-router";

// import Productos from "../components/Productos.vue";
import productos from '../components/productos.vue';
import dashboard from '../components/dashboard.vue';
import comandas from '../components/comandas.vue';
import miCuenta from '../components/miCuenta.vue';
import Ubicacion from '../components/ubicacion.vue';
import publicar from '../components/publicar.vue';
import mensaje from '../components/mensaje.vue';
import mapa from '../components/mapa.vue';


const routes = [
    {
        path: "/login",
        name: "login",
        component: login,
    },
    {
        path: '/ubicacion',
        name: 'ubicacion',
        component: Ubicacion
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: dashboard
    },
    {
        path: '/productos',
        name: 'productos',
        component: productos
    },
    {
        path: '/mapa',
        name: 'mapa',
        component: mapa
    },
    {
        path: '/mensajes',
        name: 'mensajes',
        component: mensaje
    },
    {
        path: '/comandas',
        name: 'comandas',
        component: comandas
    },
    {
        path: '/publicar',
        name: 'publicar',
        component: publicar
    },
    {
        path: '/cuenta',
        name: 'cuenta',
        component: miCuenta
    },
    
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
