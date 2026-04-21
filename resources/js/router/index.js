import { createRouter, createWebHistory } from "vue-router";
import LandingPage from "../pages/LandingPage.vue";

const routes = [
    {
        path: "/",
        name: "landing-page",
        component: LandingPage,
        meta: { layout: "none" },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
