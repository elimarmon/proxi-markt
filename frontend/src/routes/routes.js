import login from "../views/login.vue";
import { createRouter, createWebHistory } from "vue-router";
import productos from '../components/productos.vue';
import dashboard from '../components/dashboard.vue';
import comandas from '../components/comandas.vue';
import miCuenta from '../components/miCuenta.vue';
import Ubicacion from '../components/ubicacion.vue';
import publicar from '../components/publicar.vue';
import mensaje from '../components/mensaje.vue';
import mapa from '../components/mapa.vue';
import DetalleProducto from "../components/DetalleProducto.vue";
import principal from '../components/principal.vue';

const routes = [
    {
        path: "/",
        name: "principal",
        component: principal
    },
    {
        path: "/login",
        name: "login",
        component: login,
    },
    {
        path: "/productos/:id",
        name: "detalle-productos",
        component: DetalleProducto
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
        path: '/productos/:id/editar',
        name: 'editar_producto',
        component: EditarProducto,
        props: true
    },
    // {
    //     path: '/mensajes',
    //     name: 'mensajes',
    //     component: mensajes
    // },
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
    {
        path: '/ubicacion',
        name: 'ubicacion',
        component: Ubicacion
    }
    
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
