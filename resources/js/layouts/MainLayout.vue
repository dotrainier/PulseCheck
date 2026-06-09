<template>
    <div class="min-h-screen bg-[#070D1A] text-white">
        <!-- Mobile top bar -->
        <div
            class="lg:hidden fixed top-0 left-0 right-0 h-14 bg-[#0D1828] border-b border-white/8 z-40 flex items-center px-4 gap-3 shrink-0"
        >
            <button
                @click="sidebarOpen = !sidebarOpen"
                class="p-2 rounded-lg hover:bg-white/5 transition-colors"
                aria-label="Toggle menu"
            >
                <Bars3Icon class="w-5 h-5" />
            </button>
            <div class="flex items-center gap-2">
                <div
                    class="w-6 h-6 rounded-md bg-amber-500 flex items-center justify-center relative shadow-lg shadow-amber-500/30"
                >
                    <div
                        class="w-1.5 h-1.5 rounded-full bg-white animate-ping absolute"
                    ></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-white relative"></div>
                </div>
                <span
                    class="text-base font-bold tracking-wide"
                    :style="{ fontFamily: 'Space Mono, monospace' }"
                    >PulseCheck</span
                >
            </div>
        </div>

        <!-- Sidebar backdrop (mobile) -->
        <Transition name="fade">
            <div
                v-if="sidebarOpen"
                class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40"
                @click="sidebarOpen = false"
            ></div>
        </Transition>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 w-64 xl:w-72 bg-[#0D1828] border-r border-white/8 z-50 flex flex-col transition-transform duration-300 ease-in-out"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            <!-- Logo -->
            <div
                class="h-16 flex items-center gap-2.5 px-6 border-b border-white/8 shrink-0"
            >
                <div class="relative">
                    <div
                        class="w-7 h-7 rounded-md bg-amber-500 flex items-center justify-center shadow-lg shadow-amber-500/30"
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
                <button
                    @click="sidebarOpen = false"
                    class="ml-auto lg:hidden p-1.5 rounded-lg hover:bg-white/5 transition-colors"
                    aria-label="Close menu"
                >
                    <XMarkIcon class="w-4 h-4 text-gray-400" />
                </button>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
                <router-link
                    v-for="item in navigation"
                    :key="item.name"
                    :to="item.path"
                    @click="sidebarOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all"
                    :class="{
                        'bg-amber-500/15 text-amber-300 border border-amber-500/25 shadow-sm':
                            isActive(item.path),
                        'text-gray-400 hover:text-white hover:bg-white/5':
                            !isActive(item.path),
                    }"
                >
                    <component :is="item.icon" class="w-5 h-5 shrink-0" />
                    <span>{{ item.name }}</span>
                </router-link>
            </nav>

            <!-- User Section -->
            <div class="p-4 border-t border-white/8 shrink-0">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div
                        class="w-8 h-8 rounded-full bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-sm font-bold text-amber-400 shrink-0"
                    >
                        {{ userInitial }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium truncate">
                            {{ authStore.user?.name ?? 'Account' }}
                        </div>
                        <div class="text-xs text-gray-500 truncate">
                            {{ authStore.user?.email ?? '' }}
                        </div>
                    </div>
                    <button
                        @click="handleLogout"
                        title="Sign out"
                        class="p-1.5 rounded-lg text-gray-500 hover:text-white hover:bg-white/10 transition-all shrink-0"
                    >
                        <ArrowRightOnRectangleIcon class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <main class="lg:pl-64 xl:pl-72 pt-14 lg:pt-0 min-w-0">
            <router-view />
        </main>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import {
    ChartBarIcon,
    ComputerDesktopIcon,
    ExclamationTriangleIcon,
    ArrowRightOnRectangleIcon,
    Bars3Icon,
    XMarkIcon,
} from "@heroicons/vue/24/solid";
import { useAuthStore } from "@/stores/auth";

const route = useRoute();
const authStore = useAuthStore();
const sidebarOpen = ref(false);

const userInitial = computed(() => {
    const name = authStore.user?.name;
    return name ? name.charAt(0).toUpperCase() : "U";
});

const navigation = [
    { name: "Dashboard", path: "/dashboard", icon: ChartBarIcon },
    { name: "Monitors", path: "/monitors", icon: ComputerDesktopIcon },
    { name: "Incidents", path: "/incidents", icon: ExclamationTriangleIcon },
];

const isActive = (path) => route.path.startsWith(path);

const handleLogout = () => authStore.logout();

onMounted(() => {
    if (!authStore.user && authStore.token) {
        authStore.fetchUser();
    }
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
