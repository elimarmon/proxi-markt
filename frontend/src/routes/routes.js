import login from "../views/login.vue";
import PerfilUsuario from "../components/PerfilUsuario.vue";
import { createRouter, createWebHistory } from "vue-router";
import Productos from "@/components/Productos.vue";

const routes = [
    { path: "/", name: "/", component: Productos },
    {
        path: "/login",
        name: "login",
        component: login,
    },
    {
        path: "/mapa",
        name: "mapa",
        component: PerfilUsuario,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
