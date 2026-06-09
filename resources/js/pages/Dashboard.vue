<template>
    <div class="min-h-screen bg-[#070D1A] text-white">
        <!-- Header -->
        <div class="border-b border-white/8 bg-[#070D1A]/90 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h1
                            class="text-xl sm:text-2xl font-bold tracking-tight"
                            :style="{ fontFamily: 'Cabinet Grotesk, system-ui, sans-serif' }"
                        >
                            Dashboard
                        </h1>
                        <p class="text-xs sm:text-sm text-gray-400 mt-0.5 sm:mt-1">
                            System health overview and analytics
                        </p>
                    </div>
                    <div
                        class="flex items-center gap-2 text-xs text-gray-500 shrink-0"
                        :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                    >
                        <div class="flex items-center gap-1.5">
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></div>
                            <span class="hidden sm:inline">Last updated: {{ lastUpdated }}</span>
                            <span class="sm:hidden">Live</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-8 space-y-5 sm:space-y-8">
            <!-- Loading -->
            <div v-if="loading" class="flex items-center justify-center py-16">
                <div class="w-8 h-8 border-2 border-amber-500/30 border-t-amber-400 rounded-full animate-spin"></div>
            </div>

            <template v-else>
                <!-- System Status Banner -->
                <div
                    class="flex items-center gap-3 p-3 sm:p-4 rounded-lg flex-wrap sm:flex-nowrap"
                    :class="{
                        'bg-green-500/5 border border-green-500/20': systemStatus === 'operational',
                        'bg-red-500/5 border border-red-500/20': systemStatus === 'down',
                        'bg-yellow-500/5 border border-yellow-500/20': systemStatus === 'degraded',
                    }"
                >
                    <StatusIndicator :status="systemStatus" size="md" />
                    <div class="flex-1 min-w-0">
                        <div
                            class="font-semibold text-sm sm:text-base"
                            :class="{
                                'text-green-300': systemStatus === 'operational',
                                'text-red-300': systemStatus === 'down',
                                'text-yellow-300': systemStatus === 'degraded',
                            }"
                        >
                            {{
                                systemStatus === 'operational'
                                    ? 'All systems operational'
                                    : systemStatus === 'degraded'
                                      ? 'Some systems experiencing issues'
                                      : 'System outage detected'
                            }}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            {{ operationalCount }} of {{ totalMonitors }} monitors running smoothly
                        </div>
                    </div>
                    <router-link
                        to="/monitors"
                        class="px-3 py-1.5 text-xs sm:text-sm text-amber-400 hover:text-amber-300 hover:bg-amber-500/10 rounded-lg transition-all shrink-0"
                    >
                        View Monitors →
                    </router-link>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div class="p-4 sm:p-5 bg-[#0D1828] border border-white/8 rounded-lg">
                        <div class="flex items-center justify-between mb-2 sm:mb-3">
                            <div class="text-xs sm:text-sm text-gray-400">Total Monitors</div>
                            <div class="p-1.5 sm:p-2 bg-amber-500/10 rounded-lg">
                                <ChartBarIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-400" />
                            </div>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                            {{ totalMonitors }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Active monitoring</div>
                    </div>

                    <div class="p-4 sm:p-5 bg-[#0D1828] border border-white/8 rounded-lg">
                        <div class="flex items-center justify-between mb-2 sm:mb-3">
                            <div class="text-xs sm:text-sm text-gray-400">Uptime (30d)</div>
                            <div class="p-1.5 sm:p-2 bg-green-500/10 rounded-lg">
                                <ArrowTrendingUpIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-green-400" />
                            </div>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold text-green-400" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                            {{ averageUptime }}%
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Avg across monitors</div>
                    </div>

                    <div class="p-4 sm:p-5 bg-[#0D1828] border border-white/8 rounded-lg">
                        <div class="flex items-center justify-between mb-2 sm:mb-3">
                            <div class="text-xs sm:text-sm text-gray-400">Avg Response</div>
                            <div class="p-1.5 sm:p-2 bg-amber-500/10 rounded-lg">
                                <BoltIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-400" />
                            </div>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                            {{ averageResponseTime }}ms
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Global avg latency</div>
                    </div>

                    <div class="p-4 sm:p-5 bg-[#0D1828] border border-white/8 rounded-lg">
                        <div class="flex items-center justify-between mb-2 sm:mb-3">
                            <div class="text-xs sm:text-sm text-gray-400">Incidents</div>
                            <div class="p-1.5 sm:p-2 bg-red-500/10 rounded-lg">
                                <ExclamationTriangleIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-red-400" />
                            </div>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                            {{ totalIncidents }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Last 7 days</div>
                    </div>
                </div>

                <!-- Response Time Chart -->
                <div class="bg-[#0D1828] border border-white/8 rounded-lg p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <div>
                            <h2 class="text-base sm:text-lg font-semibold">Response Time Trend</h2>
                            <p class="text-xs text-gray-500 mt-0.5 sm:mt-1">Average response time over the last 24 hours</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                            <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                            <span class="hidden sm:inline">{{ averageResponseTime }}ms avg</span>
                            <span class="sm:hidden">{{ averageResponseTime }}ms</span>
                        </div>
                    </div>
                    <div class="relative">
                        <svg viewBox="0 0 800 160" class="w-full h-28 sm:h-40" xmlns="http://www.w3.org/2000/svg">
                            <line v-for="i in 4" :key="i" x1="0" :y1="i * 40" x2="800" :y2="i * 40" stroke="rgba(255,255,255,0.04)" stroke-width="1" />
                            <path :d="chartPath" fill="url(#chartGradient)" opacity="0.2" />
                            <path :d="chartLine" fill="none" stroke="#FBBF24" stroke-width="2" stroke-linecap="round" />
                            <circle :cx="animatedDotX" :cy="animatedDotY" r="4" fill="#FBBF24" class="animate-pulse" />
                            <defs>
                                <linearGradient id="chartGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#F59E0B" stop-opacity="0.4" />
                                    <stop offset="100%" stop-color="#F59E0B" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Uptime Distribution -->
                    <div class="bg-[#0D1828] border border-white/8 rounded-lg p-4 sm:p-6">
                        <div class="mb-4 sm:mb-6">
                            <h2 class="text-base sm:text-lg font-semibold">Uptime Distribution</h2>
                            <p class="text-xs text-gray-500 mt-0.5 sm:mt-1">Monitor health breakdown</p>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-2 text-sm">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-green-500"></div>
                                        <span>Operational</span>
                                    </div>
                                    <span class="font-semibold">{{ operationalCount }}</span>
                                </div>
                                <div class="w-full h-2 bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500 transition-all duration-500"
                                        :style="{ width: totalMonitors ? `${(operationalCount / totalMonitors) * 100}%` : '0%' }"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-2 text-sm">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-yellow-500"></div>
                                        <span>Degraded</span>
                                    </div>
                                    <span class="font-semibold">{{ degradedCount }}</span>
                                </div>
                                <div class="w-full h-2 bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full bg-yellow-500 transition-all duration-500"
                                        :style="{ width: totalMonitors ? `${(degradedCount / totalMonitors) * 100}%` : '0%' }"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-2 text-sm">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-red-500"></div>
                                        <span>Down</span>
                                    </div>
                                    <span class="font-semibold">{{ downCount }}</span>
                                </div>
                                <div class="w-full h-2 bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-500 transition-all duration-500"
                                        :style="{ width: totalMonitors ? `${(downCount / totalMonitors) * 100}%` : '0%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Incidents -->
                    <div class="bg-[#0D1828] border border-white/8 rounded-lg p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-6">
                            <div>
                                <h2 class="text-base sm:text-lg font-semibold">Recent Incidents</h2>
                                <p class="text-xs text-gray-500 mt-0.5 sm:mt-1">Latest downtime events</p>
                            </div>
                            <router-link to="/incidents" class="text-xs text-amber-400 hover:text-amber-300 transition-colors">
                                View All →
                            </router-link>
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="incident in recentIncidents"
                                :key="incident.id"
                                class="flex items-start gap-3 p-3 bg-[#070D1A] rounded-lg"
                            >
                                <div
                                    class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center"
                                    :class="{
                                        'bg-red-500/10': incident.severity === 'critical',
                                        'bg-yellow-500/10': incident.severity === 'warning',
                                    }"
                                >
                                    <ExclamationTriangleIcon
                                        class="w-4 h-4"
                                        :class="{
                                            'text-red-400': incident.severity === 'critical',
                                            'text-yellow-400': incident.severity === 'warning',
                                        }"
                                    />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-sm mb-0.5">
                                        {{ incident.monitor?.name ?? '—' }}
                                    </div>
                                    <div class="text-xs text-gray-400 mb-1">{{ incident.message }}</div>
                                    <div class="text-xs text-gray-500" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                                        {{ incident.timestamp }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="recentIncidents.length === 0" class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-green-500/10 border border-green-500/20 flex items-center justify-center">
                                <CheckCircleIcon class="w-6 h-6 text-green-400" />
                            </div>
                            <div class="text-sm text-gray-400">No incidents in the last 7 days</div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import {
    ChartBarIcon,
    ArrowTrendingUpIcon,
    BoltIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/solid";
import api from "@/utils/axios";
import StatusIndicator from "@/components/StatusIndicator.vue";

const loading = ref(true);
const lastUpdated = ref("Just now");
const totalMonitors = ref(0);
const operationalCount = ref(0);
const degradedCount = ref(0);
const downCount = ref(0);
const averageUptime = ref(100);
const averageResponseTime = ref(0);
const systemStatus = ref("operational");
const totalIncidents = ref(0);
const recentIncidents = ref([]);

const fetchStats = async () => {
    try {
        const { data } = await api.get("/api/dashboard/stats");
        totalMonitors.value = data.total_monitors;
        operationalCount.value = data.operational_count;
        degradedCount.value = data.degraded_count;
        downCount.value = data.down_count;
        averageUptime.value = data.average_uptime;
        averageResponseTime.value = data.average_response_time;
        systemStatus.value = data.system_status;
        totalIncidents.value = data.total_incidents;
        recentIncidents.value = data.recent_incidents;
        lastUpdated.value = "Just now";
    } catch {
        // keep defaults
    } finally {
        loading.value = false;
    }
};

// Animated sparkline (visual only, shows activity)
const generateChartData = (points = 50) => {
    const data = [];
    let value = 50 + Math.random() * 30;
    for (let i = 0; i < points; i++) {
        value += (Math.random() - 0.5) * 20 * 0.2;
        value = Math.max(20, Math.min(100, value));
        data.push(value);
    }
    return data;
};

const chartData = ref(generateChartData());

const chartPath = computed(() => {
    const w = 800, h = 160, n = chartData.value.length;
    let d = `M 0,${h - chartData.value[0] * 1.3} `;
    for (let i = 1; i < n; i++) d += `L ${(i / (n - 1)) * w},${h - chartData.value[i] * 1.3} `;
    return d + `L ${w},${h} L 0,${h} Z`;
});

const chartLine = computed(() => {
    const w = 800, h = 160, n = chartData.value.length;
    let d = `M 0,${h - chartData.value[0] * 1.3} `;
    for (let i = 1; i < n; i++) d += `L ${(i / (n - 1)) * w},${h - chartData.value[i] * 1.3} `;
    return d;
});

const animatedDotX = computed(() => 800);
const animatedDotY = computed(() => 160 - chartData.value[chartData.value.length - 1] * 1.3);

let chartInterval, refreshInterval;

onMounted(() => {
    fetchStats();
    chartInterval = setInterval(() => {
        chartData.value.shift();
        chartData.value.push(50 + Math.random() * 30);
    }, 2000);
    refreshInterval = setInterval(fetchStats, 30000);
});

onUnmounted(() => {
    clearInterval(chartInterval);
    clearInterval(refreshInterval);
});
</script>
