<template>
    <div class="min-h-screen bg-[#0D0D12] text-white">
        <!-- Header -->
        <div class="border-b border-white/10 bg-[#0D0D12]/90 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div v-if="loading" class="h-16 flex items-center">
                    <div class="w-6 h-6 border-2 border-cyan-500/30 border-t-cyan-400 rounded-full animate-spin"></div>
                </div>

                <div v-else-if="monitor" class="flex items-start justify-between">
                    <div class="flex-1">
                        <button @click="goBack" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition-colors mb-4">
                            <ChevronLeftIcon class="w-4 h-4" />
                            <span>Back to Monitors</span>
                        </button>
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 mt-1 relative flex items-center justify-center">
                                <div v-if="monitor.status === 'operational'" class="w-3 h-3 rounded-full bg-cyan-400 animate-ping absolute"></div>
                                <div class="w-3 h-3 rounded-full" :class="{
                                    'bg-cyan-400': monitor.status === 'operational',
                                    'bg-red-400': monitor.status === 'down',
                                    'bg-yellow-400': monitor.status === 'degraded',
                                    'bg-gray-400': monitor.status === 'pending',
                                }"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2 flex-wrap">
                                    <h1 class="text-2xl font-bold tracking-tight" :style="{ fontFamily: 'Cabinet Grotesk, system-ui, sans-serif' }">
                                        {{ monitor.name }}
                                    </h1>
                                    <span class="px-2 py-1 text-xs rounded-md capitalize font-medium" :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                                        :class="{
                                            'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20': monitor.status === 'operational',
                                            'bg-red-500/10 text-red-400 border border-red-500/20': monitor.status === 'down',
                                            'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20': monitor.status === 'degraded',
                                            'bg-gray-500/10 text-gray-400 border border-gray-500/20': monitor.status === 'pending',
                                        }">{{ monitor.status }}</span>
                                </div>
                                <p class="text-sm text-gray-400" :style="{ fontFamily: 'JetBrains Mono, monospace' }">{{ monitor.url }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="runCheckNow" :disabled="checking"
                            class="px-3 py-2 bg-white/5 border border-white/10 text-sm font-medium rounded-lg hover:bg-white/10 transition-all flex items-center gap-2">
                            <ArrowPathIcon class="w-4 h-4 shrink-0" :class="{ 'animate-spin': checking }" />
                            <span class="hidden sm:inline">Check Now</span>
                        </button>
                        <button @click="confirmDelete"
                            class="px-3 py-2 bg-red-500/10 border border-red-500/20 text-sm font-medium rounded-lg hover:bg-red-500/20 transition-all flex items-center gap-2 text-red-400">
                            <TrashIcon class="w-4 h-4 shrink-0" />
                            <span class="hidden sm:inline">Delete</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div v-if="monitor" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <!-- Key Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="p-4 bg-[#16161E] border border-white/10 rounded-lg" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                    <div class="text-xs text-gray-500 mb-1">Uptime (30d)</div>
                    <div class="text-2xl font-bold text-emerald-400">{{ monitor.uptime }}%</div>
                </div>
                <div class="p-4 bg-[#16161E] border border-white/10 rounded-lg" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                    <div class="text-xs text-gray-500 mb-1">Avg Response</div>
                    <div class="text-2xl font-bold">{{ monitor.avg_response_time ? `${monitor.avg_response_time}ms` : '—' }}</div>
                </div>
                <div class="p-4 bg-[#16161E] border border-white/10 rounded-lg" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                    <div class="text-xs text-gray-500 mb-1">Check Interval</div>
                    <div class="text-2xl font-bold">{{ monitor.check_interval }}</div>
                </div>
                <div class="p-4 bg-[#16161E] border border-white/10 rounded-lg" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                    <div class="text-xs text-gray-500 mb-1">Last Check</div>
                    <div class="text-lg font-bold">{{ timeAgo(monitor.last_checked_at) }}</div>
                </div>
                <div class="p-4 bg-[#16161E] border border-white/10 rounded-lg" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                    <div class="text-xs text-gray-500 mb-1">Total Checks</div>
                    <div class="text-2xl font-bold">{{ monitor.total_checks }}</div>
                </div>
            </div>

            <!-- SSL Info -->
            <div v-if="monitor.track_ssl && monitor.ssl_expiry_date" class="p-5 rounded-lg border"
                :class="{ 'bg-orange-500/5 border-orange-500/20': monitor.ssl_expiring, 'bg-emerald-500/5 border-emerald-500/20': !monitor.ssl_expiring }">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                        :class="{ 'bg-orange-500/20': monitor.ssl_expiring, 'bg-emerald-500/20': !monitor.ssl_expiring }">
                        <ShieldCheckIcon class="w-5 h-5" :class="{ 'text-orange-400': monitor.ssl_expiring, 'text-emerald-400': !monitor.ssl_expiring }" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold mb-1" :class="{ 'text-orange-300': monitor.ssl_expiring, 'text-emerald-300': !monitor.ssl_expiring }">
                            {{ monitor.ssl_expiring ? 'SSL Certificate Expiring Soon' : 'SSL Certificate Valid' }}
                        </h3>
                        <div class="text-sm text-gray-400 space-y-1" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                            <div><span class="text-gray-500">Expires:</span> {{ monitor.ssl_expiry_date }}</div>
                            <div><span class="text-gray-500">Issuer:</span> {{ monitor.ssl_issuer }}</div>
                            <div>
                                <span class="text-gray-500">Days remaining:</span>
                                <span :class="{ 'text-orange-400': monitor.ssl_expiring, 'text-emerald-400': !monitor.ssl_expiring }">
                                    {{ monitor.ssl_days_remaining }} days
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Response Time Chart -->
            <div class="bg-[#16161E] border border-white/10 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold">Response Time (24h)</h2>
                        <p class="text-xs text-gray-500 mt-1">Response time over the last 24 hours</p>
                    </div>
                    <div class="hidden sm:flex items-center gap-4" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                        <div class="text-xs text-gray-500"><span class="text-white font-semibold">{{ chartStats.min }}ms</span> min</div>
                        <div class="text-xs text-gray-500"><span class="text-white font-semibold">{{ chartStats.avg }}ms</span> avg</div>
                        <div class="text-xs text-gray-500"><span class="text-white font-semibold">{{ chartStats.max }}ms</span> max</div>
                    </div>
                </div>
                <svg viewBox="0 0 1000 200" class="w-full h-48" xmlns="http://www.w3.org/2000/svg">
                    <line v-for="i in 5" :key="i" x1="0" :y1="i * 40" x2="1000" :y2="i * 40" stroke="rgba(255,255,255,0.03)" stroke-width="1" />
                    <path :d="responseChartPath" fill="url(#responseGradient)" opacity="0.3" />
                    <path :d="responseChartLine" fill="none" stroke="#22d3ee" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    <circle :cx="dotX" :cy="dotY" r="5" fill="#22d3ee" class="animate-pulse" />
                    <defs>
                        <linearGradient id="responseGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#22d3ee" stop-opacity="0.6" />
                            <stop offset="100%" stop-color="#22d3ee" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                </svg>
                <div class="flex justify-between text-xs text-gray-500 mt-2" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                    <span>24h ago</span><span>18h ago</span><span>12h ago</span><span>6h ago</span><span>now</span>
                </div>
            </div>

            <!-- Uptime History -->
            <div class="bg-[#16161E] border border-white/10 rounded-lg p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold">Uptime History (30 days)</h2>
                </div>
                <div class="flex items-end gap-1 h-32">
                    <div v-for="(day, idx) in uptimeHistory" :key="idx" class="flex-1 group relative">
                        <div class="w-full rounded-t transition-all cursor-pointer hover:opacity-80"
                            :class="{
                                'bg-emerald-500': day.uptime === 100,
                                'bg-cyan-500': day.uptime >= 99 && day.uptime < 100,
                                'bg-yellow-500': day.uptime >= 95 && day.uptime < 99,
                                'bg-red-500': day.uptime < 95,
                            }"
                            :style="{ height: `${day.uptime}%` }"></div>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-[#0D0D12] border border-white/10 rounded text-xs whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10"
                            :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                            <div class="font-semibold">{{ day.uptime }}%</div>
                            <div class="text-gray-500">{{ day.date }}</div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-4 text-xs" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                    <span class="text-gray-500">30 days ago</span>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-emerald-500"></div><span class="text-gray-400">100%</span></div>
                        <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-cyan-500"></div><span class="text-gray-400">99–99.9%</span></div>
                        <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-yellow-500"></div><span class="text-gray-400">95–99%</span></div>
                        <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-red-500"></div><span class="text-gray-400">&lt;95%</span></div>
                    </div>
                    <span class="text-gray-500">Today</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Checks -->
                <div class="bg-[#16161E] border border-white/10 rounded-lg p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold">Recent Checks</h2>
                        <p class="text-xs text-gray-500 mt-1">Latest health check results</p>
                    </div>
                    <div v-if="checksLoading" class="flex justify-center py-8">
                        <div class="w-6 h-6 border-2 border-cyan-500/30 border-t-cyan-400 rounded-full animate-spin"></div>
                    </div>
                    <div v-else class="space-y-2">
                        <div v-for="check in recentChecks" :key="check.id" class="flex items-center justify-between p-3 bg-[#0D0D12] rounded-lg">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-2 h-2 rounded-full shrink-0"
                                    :class="{ 'bg-cyan-400': check.success, 'bg-red-400': !check.success }"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs text-gray-500" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                                        {{ timeAgo(check.created_at) }}
                                    </div>
                                    <div class="text-sm" :class="{ 'text-white': check.success, 'text-red-400': !check.success }">
                                        {{ check.message }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm font-semibold" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                                {{ check.success ? `${check.response_time}ms` : '✗' }}
                            </div>
                        </div>
                        <div v-if="recentChecks.length === 0" class="text-center py-8 text-sm text-gray-500">No checks recorded yet</div>
                    </div>
                </div>

                <!-- Incidents -->
                <div class="bg-[#16161E] border border-white/10 rounded-lg p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold">Incidents (Last 7 days)</h2>
                        <p class="text-xs text-gray-500 mt-1">Downtime events for this monitor</p>
                    </div>
                    <div class="space-y-3">
                        <div v-for="incident in monitorIncidents" :key="incident.id"
                            class="p-4 bg-[#0D0D12] rounded-lg border-l-2"
                            :class="{ 'border-red-500': incident.severity === 'critical', 'border-yellow-500': incident.severity === 'warning' }">
                            <div class="flex items-start justify-between mb-2">
                                <span class="px-2 py-0.5 text-xs rounded-md capitalize font-medium" :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                                    :class="{
                                        'bg-red-500/10 text-red-400 border border-red-500/20': incident.severity === 'critical',
                                        'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20': incident.severity === 'warning',
                                    }">{{ incident.severity }}</span>
                                <span class="text-xs text-gray-500" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                                    {{ timeAgo(incident.created_at) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-300 mb-2">{{ incident.message }}</div>
                            <div class="text-xs text-gray-500" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                                Duration: {{ incident.duration ?? 'Ongoing' }}
                            </div>
                        </div>
                    </div>
                    <div v-if="monitorIncidents.length === 0" class="text-center py-8">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-linear-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center">
                            <CheckCircleIcon class="w-6 h-6 text-emerald-400" />
                        </div>
                        <div class="text-sm text-gray-400">No incidents in the last 7 days</div>
                    </div>
                </div>
            </div>

            <!-- Configuration -->
            <div class="bg-[#16161E] border border-white/10 rounded-lg p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold">Configuration</h2>
                    <p class="text-xs text-gray-500 mt-1">Current monitor settings</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                    <div class="p-4 bg-[#0D0D12] rounded-lg"><div class="text-xs text-gray-500 mb-1">URL</div><div class="text-sm text-white break-all">{{ monitor.url }}</div></div>
                    <div class="p-4 bg-[#0D0D12] rounded-lg"><div class="text-xs text-gray-500 mb-1">Check Interval</div><div class="text-sm text-white">{{ monitor.check_interval }}</div></div>
                    <div class="p-4 bg-[#0D0D12] rounded-lg"><div class="text-xs text-gray-500 mb-1">Expected Status Code</div><div class="text-sm text-white">{{ monitor.expected_status_code ?? 'Any 2xx' }}</div></div>
                    <div class="p-4 bg-[#0D0D12] rounded-lg"><div class="text-xs text-gray-500 mb-1">Timeout</div><div class="text-sm text-white">{{ monitor.timeout }}s</div></div>
                    <div class="p-4 bg-[#0D0D12] rounded-lg"><div class="text-xs text-gray-500 mb-1">SSL Tracking</div><div class="text-sm text-white">{{ monitor.track_ssl ? 'Enabled' : 'Disabled' }}</div></div>
                    <div class="p-4 bg-[#0D0D12] rounded-lg"><div class="text-xs text-gray-500 mb-1">Created</div><div class="text-sm text-white">{{ formatDate(monitor.created_at) }}</div></div>
                </div>
            </div>
        </div>

        <div v-else-if="!loading" class="max-w-7xl mx-auto px-4 py-16 text-center">
            <p class="text-gray-400">Monitor not found.</p>
            <button @click="goBack" class="mt-4 text-cyan-400 hover:text-cyan-300 text-sm">← Back to Monitors</button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
    ChevronLeftIcon,
    TrashIcon,
    ShieldCheckIcon,
    CheckCircleIcon,
    ArrowPathIcon,
} from "@heroicons/vue/24/solid";
import api from "@/utils/axios";
import { useMonitors } from "@/composables/useMonitors";
import { timeAgo } from "@/utils/timeAgo";

const route = useRoute();
const router = useRouter();
const { deleteMonitor, runCheck } = useMonitors();

const loading = ref(true);
const checksLoading = ref(true);
const checking = ref(false);
const monitor = ref(null);
const uptimeHistory = ref([]);
const responseHistory = ref([]);
const recentChecks = ref([]);
const monitorIncidents = ref([]);

const monitorId = computed(() => Number(route.params.id));

const formatDate = (dateStr) => {
    if (!dateStr) return "—";
    return new Date(dateStr).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
};

const fetchMonitor = async () => {
    loading.value = true;
    try {
        const { data } = await api.get(`/api/monitors/${monitorId.value}`);
        monitor.value = data.data;
        uptimeHistory.value = data.uptime_history ?? [];
        responseHistory.value = data.response_history ?? [];
    } catch {
        monitor.value = null;
    } finally {
        loading.value = false;
    }
};

const fetchChecks = async () => {
    checksLoading.value = true;
    try {
        const { data } = await api.get(`/api/monitors/${monitorId.value}/checks`);
        recentChecks.value = data.data.slice(0, 10);
    } catch {
        recentChecks.value = [];
    } finally {
        checksLoading.value = false;
    }
};

const fetchIncidents = async () => {
    try {
        const { data } = await api.get("/api/incidents", {
            params: { monitor_id: monitorId.value },
        });
        const sevenDaysAgo = Date.now() - 7 * 86400000;
        monitorIncidents.value = (data.data ?? []).filter(
            (i) => new Date(i.created_at).getTime() >= sevenDaysAgo,
        );
    } catch {
        monitorIncidents.value = [];
    }
};

// Chart values — use real response history if available, else animated fallback
const animatedData = ref(Array.from({ length: 60 }, () => 40 + Math.random() * 20));

const chartValues = computed(() =>
    responseHistory.value.length >= 2
        ? responseHistory.value.map((p) => p.value)
        : animatedData.value,
);

const chartStats = computed(() => {
    const vals = chartValues.value.filter(Boolean);
    if (!vals.length) return { min: 0, avg: 0, max: 0 };
    return {
        min: Math.min(...vals),
        avg: Math.round(vals.reduce((a, b) => a + b, 0) / vals.length),
        max: Math.max(...vals),
    };
});

const buildPath = (values, w, h, fill = false) => {
    if (!values.length) return "";
    const max = Math.max(...values, 1);
    let d = `M 0,${h - (values[0] / max) * (h - 20) - 10} `;
    for (let i = 1; i < values.length; i++) {
        d += `L ${(i / (values.length - 1)) * w},${h - (values[i] / max) * (h - 20) - 10} `;
    }
    return fill ? d + `L ${w},${h} L 0,${h} Z` : d;
};

const responseChartPath = computed(() => buildPath(chartValues.value, 1000, 200, true));
const responseChartLine = computed(() => buildPath(chartValues.value, 1000, 200, false));
const dotX = computed(() => 1000);
const dotY = computed(() => {
    const vals = chartValues.value;
    if (!vals.length) return 180;
    const max = Math.max(...vals, 1);
    return 200 - (vals[vals.length - 1] / max) * 180 - 10;
});

let chartInterval, refreshInterval;

onMounted(async () => {
    await fetchMonitor();
    await Promise.all([fetchChecks(), fetchIncidents()]);

    chartInterval = setInterval(() => {
        animatedData.value.shift();
        animatedData.value.push(40 + Math.random() * 20);
    }, 3000);

    refreshInterval = setInterval(async () => {
        await fetchMonitor();
        await fetchChecks();
    }, 30000);
});

onUnmounted(() => {
    clearInterval(chartInterval);
    clearInterval(refreshInterval);
});

const runCheckNow = async () => {
    checking.value = true;
    try {
        await runCheck(monitorId.value);
        await fetchMonitor();
        await fetchChecks();
    } catch {
        // silently fail
    } finally {
        checking.value = false;
    }
};

const confirmDelete = async () => {
    if (confirm(`Delete "${monitor.value.name}"? This will remove all history and incidents.`)) {
        try {
            await deleteMonitor(monitorId.value);
            router.push("/monitors");
        } catch {
            alert("Failed to delete monitor.");
        }
    }
};

const goBack = () => router.push("/monitors");
</script>
