<template>
    <component :is="layout">
        <router-view />
    </component>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";

import MainLayout from "./layouts/MainLayout.vue";

const route = useRoute();

const layouts = {
    main: MainLayout,
    none: "div",
};

const layout = computed(() => {
    return layouts[route.meta.layout] || MainLayout;
});
</script>
