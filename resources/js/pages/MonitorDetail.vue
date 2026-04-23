<template>
    <div class="min-h-screen bg-[#0D0D12] text-white">
        <!-- Header -->
        <div class="border-b border-white/10 bg-[#0D0D12]/90 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <!-- Back Button -->
                        <button
                            @click="goBack"
                            class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition-colors mb-4"
                        >
                            <ChevronLeftIcon class="w-4 h-4" />
                            <span>Back to Monitors</span>
                        </button>

                        <!-- Monitor Info -->
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 mt-1"
                                :class="{
                                    'relative flex items-center justify-center':
                                        monitor.status === 'operational',
                                }"
                            >
                                <div
                                    v-if="monitor.status === 'operational'"
                                    class="w-3 h-3 rounded-full bg-cyan-400 animate-ping absolute"
                                ></div>
                                <div
                                    class="w-3 h-3 rounded-full"
                                    :class="{
                                        'bg-cyan-400':
                                            monitor.status === 'operational',
                                        'bg-red-400': monitor.status === 'down',
                                        'bg-yellow-400':
                                            monitor.status === 'degraded',
                                    }"
                                ></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <h1
                                        class="text-2xl font-bold tracking-tight"
                                        :style="{
                                            fontFamily:
                                                'Cabinet Grotesk, system-ui, sans-serif',
                                        }"
                                    >
                                        {{ monitor.name }}
                                    </h1>
                                    <span
                                        class="px-2 py-1 text-xs rounded-md capitalize font-medium"
                                        :style="{
                                            fontFamily:
                                                'JetBrains Mono, monospace',
                                        }"
                                        :class="{
                                            'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20':
                                                monitor.status ===
                                                'operational',
                                            'bg-red-500/10 text-red-400 border border-red-500/20':
                                                monitor.status === 'down',
                                            'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20':
                                                monitor.status === 'degraded',
                                        }"
                                    >
                                        {{ monitor.status }}
                                    </span>
                                </div>
                                <p
                                    class="text-sm text-gray-400"
                                    :style="{
                                        fontFamily: 'JetBrains Mono, monospace',
                                    }"
                                >
                                    {{ monitor.url }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <button
                            @click="editMonitor"
                            class="px-4 py-2 bg-white/5 border border-white/10 text-sm font-medium rounded-lg hover:bg-white/10 transition-all flex items-center gap-2"
                        >
                            <PencilIcon class="w-4 h-4" />
                            <span>Edit</span>
                        </button>
                        <button
                            @click="deleteMonitor"
                            class="px-4 py-2 bg-red-500/10 border border-red-500/20 text-sm font-medium rounded-lg hover:bg-red-500/20 transition-all flex items-center gap-2 text-red-400"
                        >
                            <TrashIcon class="w-4 h-4" />
                            <span>Delete</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div
                    class="p-4 bg-[#16161E] border border-white/10 rounded-lg"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <div class="text-xs text-gray-500 mb-1">Uptime (30d)</div>
                    <div class="text-2xl font-bold text-emerald-400">
                        {{ monitor.uptime }}%
                    </div>
                </div>
                <div
                    class="p-4 bg-[#16161E] border border-white/10 rounded-lg"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <div class="text-xs text-gray-500 mb-1">Avg Response</div>
                    <div class="text-2xl font-bold">
                        {{ monitor.responseTime }}ms
                    </div>
                </div>
                <div
                    class="p-4 bg-[#16161E] border border-white/10 rounded-lg"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <div class="text-xs text-gray-500 mb-1">Check Interval</div>
                    <div class="text-2xl font-bold">
                        {{ monitor.checkInterval }}
                    </div>
                </div>
                <div
                    class="p-4 bg-[#16161E] border border-white/10 rounded-lg"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <div class="text-xs text-gray-500 mb-1">Last Check</div>
                    <div class="text-lg font-bold">{{ monitor.lastCheck }}</div>
                </div>
                <div
                    class="p-4 bg-[#16161E] border border-white/10 rounded-lg"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <div class="text-xs text-gray-500 mb-1">Total Checks</div>
                    <div class="text-2xl font-bold">
                        {{ monitor.totalChecks }}
                    </div>
                </div>
            </div>

            <!-- SSL Certificate Info (if applicable) -->
            <div
                v-if="monitor.trackSsl"
                class="p-5 rounded-lg border"
                :class="{
                    'bg-orange-500/5 border-orange-500/20': monitor.sslExpiring,
                    'bg-emerald-500/5 border-emerald-500/20':
                        !monitor.sslExpiring,
                }"
            >
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                        :class="{
                            'bg-orange-500/20': monitor.sslExpiring,
                            'bg-emerald-500/20': !monitor.sslExpiring,
                        }"
                    >
                        <ShieldCheckIcon
                            class="w-5 h-5"
                            :class="{
                                'text-orange-400': monitor.sslExpiring,
                                'text-emerald-400': !monitor.sslExpiring,
                            }"
                        />
                    </div>
                    <div class="flex-1">
                        <h3
                            class="font-semibold mb-1"
                            :class="{
                                'text-orange-300': monitor.sslExpiring,
                                'text-emerald-300': !monitor.sslExpiring,
                            }"
                        >
                            {{
                                monitor.sslExpiring
                                    ? "SSL Certificate Expiring Soon"
                                    : "SSL Certificate Valid"
                            }}
                        </h3>
                        <div
                            class="text-sm text-gray-400 space-y-1"
                            :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                        >
                            <div>
                                <span class="text-gray-500">Expires:</span>
                                {{ monitor.sslExpiryDate }}
                            </div>
                            <div>
                                <span class="text-gray-500">Issuer:</span>
                                {{ monitor.sslIssuer }}
                            </div>
                            <div>
                                <span class="text-gray-500"
                                    >Days remaining:</span
                                >
                                <span
                                    :class="{
                                        'text-orange-400': monitor.sslExpiring,
                                        'text-emerald-400':
                                            !monitor.sslExpiring,
                                    }"
                                    >{{ monitor.sslDaysRemaining }} days</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Response Time Chart -->
            <div class="bg-[#16161E] border border-white/10 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold">
                            Response Time (24h)
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Average response time over the last 24 hours
                        </p>
                    </div>
                    <div
                        class="flex items-center gap-4"
                        :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                    >
                        <div class="text-xs text-gray-500">
                            <span class="text-white font-semibold"
                                >{{ stats.minResponse }}ms</span
                            >
                            min
                        </div>
                        <div class="text-xs text-gray-500">
                            <span class="text-white font-semibold"
                                >{{ stats.avgResponse }}ms</span
                            >
                            avg
                        </div>
                        <div class="text-xs text-gray-500">
                            <span class="text-white font-semibold"
                                >{{ stats.maxResponse }}ms</span
                            >
                            max
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <svg
                        viewBox="0 0 1000 200"
                        class="w-full h-48"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <!-- Grid lines -->
                        <line
                            v-for="i in 5"
                            :key="i"
                            x1="0"
                            :y1="i * 40"
                            x2="1000"
                            :y2="i * 40"
                            stroke="rgba(255,255,255,0.03)"
                            stroke-width="1"
                        />

                        <!-- Area fill -->
                        <path
                            :d="chartPath"
                            fill="url(#responseGradient)"
                            opacity="0.3"
                        />

                        <!-- Line -->
                        <path
                            :d="chartLine"
                            fill="none"
                            stroke="#22d3ee"
                            stroke-width="2.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />

                        <!-- Data points -->
                        <circle
                            v-for="(point, idx) in chartPoints"
                            :key="idx"
                            :cx="point.x"
                            :cy="point.y"
                            r="3"
                            fill="#22d3ee"
                            class="hover:r-5 transition-all"
                        />

                        <!-- Animated dot -->
                        <circle
                            :cx="animatedDotX"
                            :cy="animatedDotY"
                            r="5"
                            fill="#22d3ee"
                            class="animate-pulse"
                        />

                        <!-- Gradient definition -->
                        <defs>
                            <linearGradient
                                id="responseGradient"
                                x1="0%"
                                y1="0%"
                                x2="0%"
                                y2="100%"
                            >
                                <stop
                                    offset="0%"
                                    stop-color="#22d3ee"
                                    stop-opacity="0.6"
                                />
                                <stop
                                    offset="100%"
                                    stop-color="#22d3ee"
                                    stop-opacity="0"
                                />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>

                <!-- Time labels -->
                <div
                    class="flex justify-between text-xs text-gray-500 mt-2"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <span>24h ago</span>
                    <span>18h ago</span>
                    <span>12h ago</span>
                    <span>6h ago</span>
                    <span>now</span>
                </div>
            </div>

            <!-- Uptime History (30 days) -->
            <div class="bg-[#16161E] border border-white/10 rounded-lg p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold">
                        Uptime History (30 days)
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Daily uptime percentage for the last 30 days
                    </p>
                </div>

                <div class="flex items-end gap-1 h-32">
                    <div
                        v-for="(day, idx) in uptimeHistory"
                        :key="idx"
                        class="flex-1 group relative"
                    >
                        <div
                            class="w-full rounded-t transition-all cursor-pointer"
                            :class="{
                                'bg-emerald-500': day.uptime === 100,
                                'bg-cyan-500':
                                    day.uptime >= 99 && day.uptime < 100,
                                'bg-yellow-500':
                                    day.uptime >= 95 && day.uptime < 99,
                                'bg-red-500': day.uptime < 95,
                                'hover:opacity-80': true,
                            }"
                            :style="{ height: `${day.uptime}%` }"
                        ></div>
                        <!-- Tooltip -->
                        <div
                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-[#0D0D12] border border-white/10 rounded text-xs whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10"
                            :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                        >
                            <div class="font-semibold">{{ day.uptime }}%</div>
                            <div class="text-gray-500">{{ day.date }}</div>
                        </div>
                    </div>
                </div>

                <!-- Legend -->
                <div
                    class="flex items-center justify-between mt-4 text-xs"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <div class="text-gray-500">30 days ago</div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-emerald-500"></div>
                            <span class="text-gray-400">100%</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-cyan-500"></div>
                            <span class="text-gray-400">99-99.9%</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-yellow-500"></div>
                            <span class="text-gray-400">95-99%</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-red-500"></div>
                            <span class="text-gray-400">&lt;95%</span>
                        </div>
                    </div>
                    <div class="text-gray-500">Today</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Checks -->
                <div class="bg-[#16161E] border border-white/10 rounded-lg p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold">Recent Checks</h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Latest health check results
                        </p>
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="check in recentChecks"
                            :key="check.id"
                            class="flex items-center justify-between p-3 bg-[#0D0D12] rounded-lg"
                        >
                            <div class="flex items-center gap-3 flex-1">
                                <div
                                    class="w-2 h-2 rounded-full flex-shrink-0"
                                    :class="{
                                        'bg-cyan-400': check.success,
                                        'bg-red-400': !check.success,
                                    }"
                                ></div>
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="text-xs text-gray-500"
                                        :style="{
                                            fontFamily:
                                                'JetBrains Mono, monospace',
                                        }"
                                    >
                                        {{ check.timestamp }}
                                    </div>
                                    <div
                                        class="text-sm"
                                        :class="{
                                            'text-white': check.success,
                                            'text-red-400': !check.success,
                                        }"
                                    >
                                        {{ check.message }}
                                    </div>
                                </div>
                            </div>
                            <div
                                class="text-sm font-semibold"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            >
                                {{
                                    check.success
                                        ? `${check.responseTime}ms`
                                        : "—"
                                }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Incidents -->
                <div class="bg-[#16161E] border border-white/10 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-semibold">
                                Incidents (Last 7 days)
                            </h2>
                            <p class="text-xs text-gray-500 mt-1">
                                Downtime events for this monitor
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="incident in incidents"
                            :key="incident.id"
                            class="p-4 bg-[#0D0D12] rounded-lg border-l-2"
                            :class="{
                                'border-red-500':
                                    incident.severity === 'critical',
                                'border-yellow-500':
                                    incident.severity === 'warning',
                            }"
                        >
                            <div class="flex items-start justify-between mb-2">
                                <span
                                    class="px-2 py-0.5 text-xs rounded-md capitalize font-medium"
                                    :style="{
                                        fontFamily: 'JetBrains Mono, monospace',
                                    }"
                                    :class="{
                                        'bg-red-500/10 text-red-400 border border-red-500/20':
                                            incident.severity === 'critical',
                                        'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20':
                                            incident.severity === 'warning',
                                    }"
                                >
                                    {{ incident.severity }}
                                </span>
                                <span
                                    class="text-xs text-gray-500"
                                    :style="{
                                        fontFamily: 'JetBrains Mono, monospace',
                                    }"
                                    >{{ incident.timestamp }}</span
                                >
                            </div>
                            <div class="text-sm text-gray-300 mb-2">
                                {{ incident.message }}
                            </div>
                            <div
                                class="text-xs text-gray-500"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            >
                                Duration: {{ incident.duration }}
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="incidents.length === 0" class="text-center py-8">
                        <div
                            class="w-12 h-12 mx-auto mb-3 rounded-full bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center"
                        >
                            <CheckCircleIcon class="w-6 h-6 text-emerald-400" />
                        </div>
                        <div class="text-sm text-gray-400">
                            No incidents in the last 7 days
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monitor Configuration -->
            <div class="bg-[#16161E] border border-white/10 rounded-lg p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold">Configuration</h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Current monitor settings
                    </p>
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <div class="p-4 bg-[#0D0D12] rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">URL</div>
                        <div class="text-sm text-white break-all">
                            {{ monitor.url }}
                        </div>
                    </div>
                    <div class="p-4 bg-[#0D0D12] rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">
                            Check Interval
                        </div>
                        <div class="text-sm text-white">
                            {{ monitor.checkInterval }}
                        </div>
                    </div>
                    <div class="p-4 bg-[#0D0D12] rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">
                            Expected Status Code
                        </div>
                        <div class="text-sm text-white">
                            {{ monitor.expectedStatusCode || "Any 2xx" }}
                        </div>
                    </div>
                    <div class="p-4 bg-[#0D0D12] rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">Timeout</div>
                        <div class="text-sm text-white">
                            {{ monitor.timeout }}s
                        </div>
                    </div>
                    <div class="p-4 bg-[#0D0D12] rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">
                            SSL Tracking
                        </div>
                        <div class="text-sm text-white">
                            {{ monitor.trackSsl ? "Enabled" : "Disabled" }}
                        </div>
                    </div>
                    <div class="p-4 bg-[#0D0D12] rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">Created</div>
                        <div class="text-sm text-white">
                            {{ monitor.createdAt }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
    ChevronLeftIcon,
    PencilIcon,
    TrashIcon,
    ShieldCheckIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/solid";

const route = useRoute();
const router = useRouter();

// Get monitor ID from route params
const monitorId = ref(route.params.id);

// Static monitor data (in real app, this would be fetched from API)
const monitors = {
    1: {
        id: 1,
        name: "Production API",
        url: "https://api.example.com/health",
        status: "operational",
        uptime: 99.98,
        responseTime: 45,
        checkInterval: "30s",
        lastCheck: "2 min ago",
        trackSsl: true,
        sslExpiring: false,
        expectedStatusCode: "200",
        timeout: 30,
        totalChecks: 86234,
        sslExpiryDate: "Aug 15, 2026",
        sslIssuer: "Let's Encrypt",
        sslDaysRemaining: 114,
        createdAt: "Jan 15, 2026",
    },
    2: {
        id: 2,
        name: "Marketing Website",
        url: "https://www.example.com",
        status: "operational",
        uptime: 100,
        responseTime: 38,
        checkInterval: "1m",
        lastCheck: "1 min ago",
        trackSsl: true,
        sslExpiring: false,
        expectedStatusCode: "200",
        timeout: 30,
        totalChecks: 43117,
        sslExpiryDate: "Sep 22, 2026",
        sslIssuer: "DigiCert",
        sslDaysRemaining: 152,
        createdAt: "Feb 10, 2026",
    },
    3: {
        id: 3,
        name: "CDN Service",
        url: "https://cdn.example.com/status",
        status: "operational",
        uptime: 99.95,
        responseTime: 52,
        checkInterval: "5m",
        lastCheck: "4 min ago",
        trackSsl: true,
        sslExpiring: true,
        expectedStatusCode: "200",
        timeout: 30,
        totalChecks: 8623,
        sslExpiryDate: "May 8, 2026",
        sslIssuer: "CloudFlare",
        sslDaysRemaining: 14,
        createdAt: "Jan 22, 2026",
    },
};

const monitor = ref(monitors[monitorId.value] || monitors[1]);

// Chart data
const generateChartData = (points = 60, volatility = 0.3) => {
    const data = [];
    let value = 40 + Math.random() * 20;

    for (let i = 0; i < points; i++) {
        value += (Math.random() - 0.5) * 20 * volatility;
        value = Math.max(20, Math.min(80, value));
        data.push(value);
    }

    return data;
};

const chartData = ref(generateChartData());

// Recent checks data
const recentChecks = ref([
    {
        id: 1,
        timestamp: "2 min ago",
        success: true,
        message: "OK - Status 200",
        responseTime: 45,
    },
    {
        id: 2,
        timestamp: "2 min 30s ago",
        success: true,
        message: "OK - Status 200",
        responseTime: 42,
    },
    {
        id: 3,
        timestamp: "3 min ago",
        success: true,
        message: "OK - Status 200",
        responseTime: 48,
    },
    {
        id: 4,
        timestamp: "3 min 30s ago",
        success: true,
        message: "OK - Status 200",
        responseTime: 43,
    },
    {
        id: 5,
        timestamp: "4 min ago",
        success: true,
        message: "OK - Status 200",
        responseTime: 46,
    },
]);

// Incidents data
const incidents = ref([
    {
        id: 1,
        severity: "warning",
        message: "High response time detected (>500ms)",
        timestamp: "2 days ago",
        duration: "12 minutes",
    },
]);

// Generate 30 days of uptime history
const uptimeHistory = ref(
    Array.from({ length: 30 }, (_, i) => ({
        date: `Day ${i + 1}`,
        uptime: i === 15 ? 95.5 : i === 8 ? 98.2 : 99.5 + Math.random() * 0.5,
    })),
);

// Stats
const stats = computed(() => ({
    minResponse: Math.min(...chartData.value).toFixed(0),
    avgResponse: (
        chartData.value.reduce((a, b) => a + b, 0) / chartData.value.length
    ).toFixed(0),
    maxResponse: Math.max(...chartData.value).toFixed(0),
}));

// Chart calculations
const chartPath = computed(() => {
    const width = 1000;
    const height = 200;
    const points = chartData.value.length;

    let path = `M 0,${height - chartData.value[0] * 2} `;

    for (let i = 1; i < points; i++) {
        const x = (i / (points - 1)) * width;
        const y = height - chartData.value[i] * 2;
        path += `L ${x},${y} `;
    }

    path += `L ${width},${height} L 0,${height} Z`;
    return path;
});

const chartLine = computed(() => {
    const width = 1000;
    const height = 200;
    const points = chartData.value.length;

    let path = `M 0,${height - chartData.value[0] * 2} `;

    for (let i = 1; i < points; i++) {
        const x = (i / (points - 1)) * width;
        const y = height - chartData.value[i] * 2;
        path += `L ${x},${y} `;
    }

    return path;
});

const chartPoints = computed(() => {
    const width = 1000;
    const height = 200;
    const points = chartData.value.length;

    return chartData.value.map((value, i) => ({
        x: (i / (points - 1)) * width,
        y: height - value * 2,
    }));
});

const animatedDotX = computed(() => {
    const width = 1000;
    const points = chartData.value.length;
    return ((points - 1) / (points - 1)) * width;
});

const animatedDotY = computed(() => {
    const height = 200;
    return height - chartData.value[chartData.value.length - 1] * 2;
});

// Animate chart data
let chartInterval;
onMounted(() => {
    chartInterval = setInterval(() => {
        chartData.value.shift();
        chartData.value.push(40 + Math.random() * 20);
    }, 3000);
});

onUnmounted(() => {
    if (chartInterval) clearInterval(chartInterval);
});

// Actions
const goBack = () => {
    router.push("/monitors");
};

const editMonitor = () => {
    console.log("Edit monitor:", monitor.value.id);
    // In real app, open edit modal or navigate to edit page
};

const deleteMonitor = () => {
    if (confirm(`Are you sure you want to delete "${monitor.value.name}"?`)) {
        console.log("Deleting monitor:", monitor.value.id);
        // In real app, call API and redirect
        router.push("/monitors");
    }
};
</script>
