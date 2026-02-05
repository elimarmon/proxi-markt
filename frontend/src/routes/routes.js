import Login from "../views/Login.vue";
import { createRouter, createWebHistory } from "vue-router";
import Productos from '../components/Productos.vue';
import Dashboard from '../components/Dashboard.vue';
import Comandas from '../components/Comandas.vue';
import MiCuenta from '../components/MiCuenta.vue';
import Ubicacion from '../components/Ubicacion.vue';
import Publicar from '../components/Publicar.vue';
import mensaje from '../components/mensaje.vue';
import Mapa from '../components/Mapa.vue';
import DetalleProducto from "../components/DetalleProducto.vue";
import Principal from '../components/Principal.vue';
import EditarProducto from "../components/EditarProducto.vue";

const routes = [
    {
        path: "/",
        name: "principal",
        component: Principal
    },
    {
        path: "/login",
        name: "login",
        component: Login,
    },
    {
        path: "/productos/:id",
        name: "detalle-productos",
        props: true,
        component: DetalleProducto
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard
    },
    {
        path: '/ubicacion',
        name: 'ubicacion',
        component: Ubicacion
    },
    {
        path: '/productos',
        name: 'productos',
        component: Productos
    },
    {
        path: '/productos/:id/editar',
        name: 'editar_producto',
        component: EditarProducto,
        props: true
    },
    {
        path: '/mensaje',
        name: 'mensaje',
        component: mensaje
    },
    {
        path: '/comandas',
        name: 'comandas',
        component: Comandas
    },
    {
        path: '/publicar',
        name: 'publicar',
        component: Publicar
    },
    {
        path: '/cuenta',
        name: 'cuenta',
        component: MiCuenta
    },
    {
        path: '/ubicacion',
        name: 'ubicacion',
        component: Ubicacion
    },
    {
        path: '/mapa',
        name: 'mapa',
        component: Mapa
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;