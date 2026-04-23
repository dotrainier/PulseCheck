import { createRouter, createWebHistory } from "vue-router";
import LandingPage from "../pages/LandingPage.vue";
import Dashboard from "../pages/Dashboard.vue";
import Monitors from "../pages/Monitors.vue";
import Incidents from "../pages/Incidents.vue";
import MonitorDetail from "../pages/MonitorDetail.vue";

const routes = [
    {
        path: "/",
        name: "landing",
        component: LandingPage,
        meta: {
            title: "PulseCheck - Monitor your websites and APIs in real time",
            layout: "none",
            requiresAuth: false,
        },
    },
    {
        path: "/dashboard",
        name: "dashboard",
        component: Dashboard,
        meta: {
            title: "Dashboard - PulseCheck",
            layout: "main",
            requiresAuth: true,
        },
    },
    {
        path: "/monitors",
        name: "monitors",
        component: Monitors,
        meta: {
            title: "Monitors - PulseCheck",
            requiresAuth: true,
        },
    },
    {
        path: "/monitors/:id",
        name: "monitor-detail",
        component: MonitorDetail,
        meta: {
            title: "Monitor Details - PulseCheck",
            requiresAuth: true,
        },
    },
    {
        path: "/incidents",
        name: "incidents",
        component: Incidents,
        meta: {
            title: "Incidents - PulseCheck",
            requiresAuth: true,
        },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    // scrollBehavior(to, from, savedPosition) {
    //     if (savedPosition) {
    //         return savedPosition;
    //     } else {
    //         return { top: 0 };
    //     }
    // },
});

// Auth guard
// router.beforeEach((to, from, next) => {
//     const isAuthenticated = !!localStorage.getItem("auth_token"); // Adjust based on your auth implementation

//     if (to.meta.title) {
//         document.title = to.meta.title;
//     }

//     if (to.meta.requiresAuth && !isAuthenticated) {
//         next({ name: "landing" });
//     } else {
//         next();
//     }
// });

export default router;
