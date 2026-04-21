<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="isOpen"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            >
                <!-- Backdrop -->
                <div
                    @click.self="close"
                    class="absolute inset-0 bg-black/80 backdrop-blur-sm"
                ></div>

                <!-- Modal -->
                <div
                    class="relative bg-[#16161E] rounded-xl border border-white/10 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <!-- Header -->
                    <div
                        class="px-6 py-4 border-b border-white/10 flex items-center justify-between"
                    >
                        <div>
                            <h2 class="text-xl font-bold mb-1">
                                Live API Monitor Demo
                            </h2>
                            <p class="text-sm text-gray-500">
                                Click any endpoint to check its status in
                                real-time
                            </p>
                        </div>
                        <button
                            @click="close"
                            class="w-8 h-8 rounded-lg hover:bg-white/5 flex items-center justify-center transition-colors group"
                        >
                            <XMarkIcon
                                class="w-5 h-5 text-gray-500 group-hover:text-white"
                            />
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                        <div class="space-y-3">
                            <!-- API Endpoint Cards -->
                            <div
                                v-for="api in apis"
                                :key="api.url"
                                class="group relative bg-[#1A1A24] rounded-lg border border-white/5 hover:border-cyan-500/30 transition-all overflow-hidden"
                            >
                                <div class="p-4">
                                    <div
                                        class="flex items-start justify-between mb-3"
                                    >
                                        <div class="flex-1 min-w-0">
                                            <div
                                                class="flex items-center gap-2 mb-1"
                                            >
                                                <span
                                                    class="text-xs px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 font-semibold"
                                                    >{{ api.method }}</span
                                                >
                                                <h3
                                                    class="text-sm font-semibold text-white truncate"
                                                >
                                                    {{ api.name }}
                                                </h3>
                                            </div>
                                            <p
                                                class="text-xs text-gray-500 break-all"
                                            >
                                                {{ api.url }}
                                            </p>
                                        </div>

                                        <!-- Status Indicator -->
                                        <div
                                            v-if="api.status"
                                            class="ml-4 flex-shrink-0"
                                        >
                                            <div
                                                v-if="api.status === 'checking'"
                                                class="flex items-center gap-2"
                                            >
                                                <div
                                                    class="w-4 h-4 border-2 border-cyan-400/30 border-t-cyan-400 rounded-full animate-spin"
                                                ></div>
                                                <span
                                                    class="text-xs text-gray-500"
                                                    >checking...</span
                                                >
                                            </div>
                                            <div
                                                v-else-if="
                                                    api.status === 'success'
                                                "
                                                class="flex items-center gap-2"
                                            >
                                                <div
                                                    class="w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center"
                                                >
                                                    <CheckCircleIcon
                                                        class="w-3 h-3 text-emerald-400"
                                                    />
                                                </div>
                                                <span
                                                    class="text-xs text-emerald-400 font-semibold"
                                                    >Online</span
                                                >
                                            </div>
                                            <div
                                                v-else-if="
                                                    api.status === 'error'
                                                "
                                                class="flex items-center gap-2"
                                            >
                                                <div
                                                    class="w-5 h-5 rounded-full bg-red-500/20 flex items-center justify-center"
                                                >
                                                    <XCircleIcon
                                                        class="w-3 h-3 text-red-400"
                                                    />
                                                </div>
                                                <span
                                                    class="text-xs text-red-400 font-semibold"
                                                    >Failed</span
                                                >
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Results -->
                                    <div
                                        v-if="api.result"
                                        class="mt-3 pt-3 border-t border-white/5 grid grid-cols-3 gap-3 text-xs"
                                    >
                                        <div>
                                            <div class="text-gray-600 mb-1">
                                                Status
                                            </div>
                                            <div
                                                :class="[
                                                    'font-semibold',
                                                    api.result.statusCode >=
                                                        200 &&
                                                    api.result.statusCode < 300
                                                        ? 'text-emerald-400'
                                                        : 'text-red-400',
                                                ]"
                                            >
                                                {{
                                                    api.result.statusCode ||
                                                    "N/A"
                                                }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-gray-600 mb-1">
                                                Response Time
                                            </div>
                                            <div
                                                class="font-semibold text-cyan-300"
                                            >
                                                {{ api.result.responseTime }}ms
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-gray-600 mb-1">
                                                Timestamp
                                            </div>
                                            <div
                                                class="font-semibold text-gray-400"
                                            >
                                                {{ api.result.timestamp }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Check Button -->
                                    <button
                                        @click="checkEndpoint(api)"
                                        :disabled="api.status === 'checking'"
                                        class="mt-3 w-full px-4 py-2 bg-gradient-to-r from-cyan-600/80 to-teal-600/80 hover:from-cyan-600 hover:to-teal-600 disabled:from-gray-600 disabled:to-gray-600 rounded-md text-sm font-semibold transition-all disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                    >
                                        <ArrowPathIcon
                                            v-if="api.status !== 'checking'"
                                            class="w-4 h-4"
                                        />
                                        <span>{{
                                            api.status === "checking"
                                                ? "Checking..."
                                                : "Check Now"
                                        }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Info Note -->
                        <div
                            class="mt-6 p-4 bg-cyan-500/5 border border-cyan-500/20 rounded-lg"
                        >
                            <div class="flex gap-3">
                                <InformationCircleIcon
                                    class="w-5 h-5 text-cyan-400 flex-shrink-0 mt-0.5"
                                />
                                <div
                                    class="text-xs text-gray-400 leading-relaxed"
                                >
                                    <span class="text-cyan-300 font-semibold"
                                        >This is a live demo.</span
                                    >
                                    Each check makes a real HTTP request to the
                                    endpoint. Response times may vary based on
                                    your network and the API's location.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref } from "vue";
import {
    XMarkIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowPathIcon,
    InformationCircleIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close"]);

const apis = ref([
    {
        name: "GitHub API",
        url: "https://api.github.com",
        method: "GET",
        status: null,
        result: null,
    },
    {
        name: "JSONPlaceholder",
        url: "https://jsonplaceholder.typicode.com/posts/1",
        method: "GET",
        status: null,
        result: null,
    },
    {
        name: "HTTPBin Status Test",
        url: "https://httpbin.org/status/200",
        method: "GET",
        status: null,
        result: null,
    },
    {
        name: "Cat Facts API",
        url: "https://catfact.ninja/fact",
        method: "GET",
        status: null,
        result: null,
    },
]);

const checkEndpoint = async (api) => {
    api.status = "checking";
    api.result = null;

    const startTime = performance.now();

    try {
        const response = await fetch(api.url, {
            method: api.method,
            mode: "cors",
        });

        const endTime = performance.now();
        const responseTime = Math.round(endTime - startTime);

        api.status = response.ok ? "success" : "error";
        api.result = {
            statusCode: response.status,
            responseTime: responseTime,
            timestamp: new Date().toLocaleTimeString(),
        };
    } catch (error) {
        const endTime = performance.now();
        const responseTime = Math.round(endTime - startTime);

        api.status = "error";
        api.result = {
            statusCode: "ERR",
            responseTime: responseTime,
            timestamp: new Date().toLocaleTimeString(),
        };
    }
};

const close = () => {
    emit("close");
};
</script>
