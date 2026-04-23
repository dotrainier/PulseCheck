<template>
    <div class="min-h-screen bg-[#0D0D12] text-white">
        <aside
            class="fixed inset-y-0 left-0 w-64 bg-[#16161E] border-r border-white/10 z-40"
        >
            <!-- Logo -->
            <div
                class="h-16 flex items-center gap-2.5 px-6 border-b border-white/10"
            >
                <div class="relative">
                    <div
                        class="w-7 h-7 rounded-md bg-gradient-to-br from-cyan-500 via-teal-500 to-emerald-500 flex items-center justify-center"
                    >
                        <div
                            class="w-2 h-2 rounded-full bg-white animate-ping absolute"
                        ></div>
                        <div
                            class="w-2 h-2 rounded-full bg-white relative"
                        ></div>
                    </div>
                </div>
                <span
                    class="text-lg font-bold tracking-wide"
                    :style="{ fontFamily: 'Space Mono, monospace' }"
                    >PulseCheck</span
                >
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-1">
                <router-link
                    v-for="item in navigation"
                    :key="item.name"
                    :to="item.path"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all"
                    :class="{
                        'bg-gradient-to-r from-cyan-600 to-teal-600 text-white shadow-lg shadow-cyan-500/25':
                            isActive(item.path),
                        'text-gray-400 hover:text-white hover:bg-white/5':
                            !isActive(item.path),
                    }"
                >
                    <component :is="item.icon" class="w-5 h-5" />
                    <span>{{ item.name }}</span>
                </router-link>
            </nav>

            <!-- User Section -->
            <div
                class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10"
            >
                <div class="flex items-center gap-3 px-3 py-2">
                    <div
                        class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-500 to-teal-500 flex items-center justify-center text-sm font-bold"
                    >
                        U
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium truncate">
                            User Account
                        </div>
                        <div class="text-xs text-gray-500">
                            user@example.com
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="pl-64">
            <router-view />
        </main>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import {
    ChartBarIcon,
    ComputerDesktopIcon,
    ExclamationTriangleIcon,
    Cog6ToothIcon,
} from "@heroicons/vue/24/solid";

const route = useRoute();

const navigation = [
    {
        name: "Dashboard",
        path: "/dashboard",
        icon: ChartBarIcon,
    },
    {
        name: "Monitors",
        path: "/monitors",
        icon: ComputerDesktopIcon,
    },
    {
        name: "Incidents",
        path: "/incidents",
        icon: ExclamationTriangleIcon,
    },
    {
        name: "Settings",
        path: "/settings",
        icon: Cog6ToothIcon,
    },
];

const isActive = (path) => {
    return route.path.startsWith(path);
};
</script>
