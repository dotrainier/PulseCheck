<template>
    <div class="min-h-screen bg-[#0D0D12] text-white">
        <!-- Header -->
        <div class="border-b border-white/10 bg-[#0D0D12]/90 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight"
                            :style="{
                                fontFamily:
                                    'Cabinet Grotesk, system-ui, sans-serif',
                            }"
                        >
                            Incidents
                        </h1>
                        <p class="text-sm text-gray-400 mt-1">
                            Downtime history and incident reports
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div
                    class="p-4 bg-[#16161E] border border-white/10 rounded-lg"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <div class="text-xs text-gray-500 mb-1">
                        Total Incidents
                    </div>
                    <div class="text-2xl font-bold">{{ totalIncidents }}</div>
                    <div class="text-xs text-gray-400 mt-1">Last 30 days</div>
                </div>
                <div
                    class="p-4 bg-[#16161E] border border-white/10 rounded-lg"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <div class="text-xs text-gray-500 mb-1">Critical</div>
                    <div class="text-2xl font-bold text-red-400">
                        {{ criticalCount }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">High priority</div>
                </div>
                <div
                    class="p-4 bg-[#16161E] border border-white/10 rounded-lg"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <div class="text-xs text-gray-500 mb-1">Avg Downtime</div>
                    <div class="text-2xl font-bold text-yellow-400">
                        {{ averageDowntime }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Per incident</div>
                </div>
                <div
                    class="p-4 bg-[#16161E] border border-white/10 rounded-lg"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <div class="text-xs text-gray-500 mb-1">MTTR</div>
                    <div class="text-2xl font-bold text-emerald-400">
                        {{ mttr }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        Mean time to recovery
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <button
                    v-for="status in statusFilters"
                    :key="status.value"
                    @click="activeFilter = status.value"
                    class="px-3 py-1.5 text-sm rounded-lg transition-all"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                    :class="{
                        'bg-gradient-to-r from-cyan-600 to-teal-600 text-white shadow-lg shadow-cyan-500/25':
                            activeFilter === status.value,
                        'bg-[#16161E] text-gray-400 hover:text-white border border-white/10':
                            activeFilter !== status.value,
                    }"
                >
                    {{ status.label }}
                </button>
            </div>

            <!-- Incidents Timeline -->
            <div class="bg-[#16161E] border border-white/10 rounded-lg p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold">Incident History</h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Chronological list of all incidents
                    </p>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="incident in filteredIncidents"
                        :key="incident.id"
                        class="relative pl-8 pb-4 border-l-2"
                        :class="{
                            'border-red-500': incident.severity === 'critical',
                            'border-yellow-500':
                                incident.severity === 'warning',
                            'border-green-500': incident.status === 'resolved',
                            'border-gray-500':
                                incident.status === 'investigating',
                        }"
                    >
                        <!-- Timeline Dot -->
                        <div
                            class="absolute left-0 -translate-x-1/2 top-0 w-4 h-4 rounded-full border-2"
                            :class="{
                                'bg-red-500 border-red-500':
                                    incident.severity === 'critical',
                                'bg-yellow-500 border-yellow-500':
                                    incident.severity === 'warning',
                                'bg-green-500 border-green-500':
                                    incident.status === 'resolved',
                                'bg-gray-500 border-gray-500':
                                    incident.status === 'investigating',
                            }"
                        ></div>

                        <!-- Incident Card -->
                        <div
                            class="bg-[#0D0D12] rounded-lg p-5 hover:bg-[#1A1A24] transition-colors"
                        >
                            <!-- Header -->
                            <div
                                class="flex items-start justify-between gap-4 mb-3"
                            >
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span
                                            class="px-2 py-0.5 text-xs rounded-md capitalize font-medium"
                                            :style="{
                                                fontFamily:
                                                    'JetBrains Mono, monospace',
                                            }"
                                            :class="{
                                                'bg-red-500/10 text-red-400 border border-red-500/20':
                                                    incident.severity ===
                                                    'critical',
                                                'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20':
                                                    incident.severity ===
                                                    'warning',
                                            }"
                                        >
                                            {{ incident.severity }}
                                        </span>
                                        <span
                                            class="px-2 py-0.5 text-xs rounded-md capitalize font-medium"
                                            :style="{
                                                fontFamily:
                                                    'JetBrains Mono, monospace',
                                            }"
                                            :class="{
                                                'bg-green-500/10 text-green-400 border border-green-500/20':
                                                    incident.status ===
                                                    'resolved',
                                                'bg-blue-500/10 text-blue-400 border border-blue-500/20':
                                                    incident.status ===
                                                    'investigating',
                                                'bg-gray-500/10 text-gray-400 border border-gray-500/20':
                                                    incident.status ===
                                                    'identified',
                                            }"
                                        >
                                            {{ incident.status }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold mb-1">
                                        {{ incident.monitor }}
                                    </h3>
                                    <p class="text-sm text-gray-400">
                                        {{ incident.message }}
                                    </p>
                                </div>
                            </div>

                            <!-- Metrics -->
                            <div
                                class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 text-xs"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            >
                                <div>
                                    <div class="text-gray-500">Started</div>
                                    <div class="font-semibold text-white">
                                        {{ incident.startTime }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Duration</div>
                                    <div class="font-semibold text-white">
                                        {{ incident.duration }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Impact</div>
                                    <div class="font-semibold text-white">
                                        {{ incident.impact }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-gray-500">
                                        Checks Failed
                                    </div>
                                    <div class="font-semibold text-white">
                                        {{ incident.failedChecks }}
                                    </div>
                                </div>
                            </div>

                            <!-- Updates Timeline -->
                            <div
                                v-if="
                                    incident.updates && incident.updates.length
                                "
                                class="border-t border-white/10 pt-4 space-y-3"
                            >
                                <div
                                    class="text-xs font-semibold text-gray-400"
                                >
                                    UPDATES
                                </div>
                                <div
                                    v-for="(update, idx) in incident.updates"
                                    :key="idx"
                                    class="flex gap-3"
                                >
                                    <div
                                        class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-gray-500 mt-1.5"
                                    ></div>
                                    <div class="flex-1">
                                        <div
                                            class="text-xs text-gray-500 mb-0.5"
                                            :style="{
                                                fontFamily:
                                                    'JetBrains Mono, monospace',
                                            }"
                                        >
                                            {{ update.timestamp }}
                                        </div>
                                        <div class="text-sm text-gray-300">
                                            {{ update.message }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Details -->
                            <div
                                v-if="incident.errorDetails"
                                class="mt-4 p-3 bg-[#16161E] rounded-lg border border-white/10"
                            >
                                <div
                                    class="text-xs font-semibold text-gray-400 mb-2"
                                >
                                    ERROR DETAILS
                                </div>
                                <code
                                    class="text-xs text-red-400 block overflow-x-auto"
                                    :style="{
                                        fontFamily: 'JetBrains Mono, monospace',
                                    }"
                                >
                                    {{ incident.errorDetails }}
                                </code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="filteredIncidents.length === 0"
                    class="text-center py-12"
                >
                    <div
                        class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center"
                    >
                        <CheckCircleIcon class="w-8 h-8 text-emerald-400" />
                    </div>
                    <h3 class="text-lg font-semibold mb-2">
                        No incidents found
                    </h3>
                    <p class="text-gray-400">
                        {{
                            activeFilter === "all"
                                ? "All systems are running smoothly"
                                : `No ${activeFilter} incidents to display`
                        }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { CheckCircleIcon } from "@heroicons/vue/24/solid";

// Filter options
const statusFilters = [
    { label: "All", value: "all" },
    { label: "Critical", value: "critical" },
    { label: "Warning", value: "warning" },
    { label: "Resolved", value: "resolved" },
    { label: "Investigating", value: "investigating" },
];

const activeFilter = ref("all");

// Static incidents data
const incidents = ref([
    {
        id: 1,
        monitor: "Legacy API",
        message: "Service unreachable - Connection timeout after 30 seconds",
        severity: "critical",
        status: "investigating",
        startTime: "2 hours ago",
        duration: "Ongoing",
        impact: "Complete outage",
        failedChecks: 24,
        errorDetails:
            "ECONNREFUSED: Connection refused at http://legacy.example.com/api",
        updates: [
            {
                timestamp: "1 hour 45 min ago",
                message:
                    "Team has been notified and is investigating the root cause.",
            },
            {
                timestamp: "1 hour 30 min ago",
                message:
                    "Identified network connectivity issues with the legacy infrastructure.",
            },
            {
                timestamp: "1 hour ago",
                message:
                    "Working with infrastructure team to restore connectivity.",
            },
        ],
    },
    {
        id: 2,
        monitor: "Auth Service",
        message: "High response time detected (>500ms average)",
        severity: "warning",
        status: "resolved",
        startTime: "5 hours ago",
        duration: "45 minutes",
        impact: "Degraded performance",
        failedChecks: 15,
        errorDetails: null,
        updates: [
            {
                timestamp: "4 hours 30 min ago",
                message:
                    "Detected elevated response times. Investigating database queries.",
            },
            {
                timestamp: "4 hours 15 min ago",
                message:
                    "Identified slow query causing bottleneck. Optimizing indexes.",
            },
            {
                timestamp: "4 hours ago",
                message:
                    "Database optimization complete. Service restored to normal.",
            },
        ],
    },
    {
        id: 3,
        monitor: "CDN Service",
        message: "SSL certificate expiring in 14 days",
        severity: "warning",
        status: "identified",
        startTime: "1 day ago",
        duration: "N/A",
        impact: "None (preventive)",
        failedChecks: 0,
        errorDetails: "SSL certificate expires on May 8, 2026 at 23:59:59 UTC",
        updates: [
            {
                timestamp: "1 day ago",
                message:
                    "SSL certificate renewal process initiated automatically.",
            },
            {
                timestamp: "18 hours ago",
                message: "Awaiting certificate authority verification.",
            },
        ],
    },
    {
        id: 4,
        monitor: "Production API",
        message: "HTTP 500 Internal Server Error returned",
        severity: "critical",
        status: "resolved",
        startTime: "2 days ago",
        duration: "1 hour 23 minutes",
        impact: "Partial outage",
        failedChecks: 83,
        errorDetails:
            "HTTP 500: Internal Server Error - Database connection pool exhausted",
        updates: [
            {
                timestamp: "2 days ago",
                message:
                    "Multiple 500 errors detected. Investigating immediately.",
            },
            {
                timestamp: "2 days ago",
                message:
                    "Root cause identified: Database connection pool exhaustion.",
            },
            {
                timestamp: "2 days ago",
                message:
                    "Increased connection pool size and restarted service. Monitoring recovery.",
            },
            {
                timestamp: "2 days ago",
                message: "All systems operational. Incident resolved.",
            },
        ],
    },
    {
        id: 5,
        monitor: "Marketing Website",
        message: "DNS resolution failure",
        severity: "critical",
        status: "resolved",
        startTime: "3 days ago",
        duration: "12 minutes",
        impact: "Complete outage",
        failedChecks: 24,
        errorDetails: "ENOTFOUND: DNS lookup failed for www.example.com",
        updates: [
            {
                timestamp: "3 days ago",
                message:
                    "DNS resolution failing. Checking DNS provider status.",
            },
            {
                timestamp: "3 days ago",
                message:
                    "Confirmed DNS provider outage. Switching to backup DNS servers.",
            },
            {
                timestamp: "3 days ago",
                message: "DNS failover complete. Service restored.",
            },
        ],
    },
    {
        id: 6,
        monitor: "Database Health",
        message: "Response time spike detected (>1000ms)",
        severity: "warning",
        status: "resolved",
        startTime: "4 days ago",
        duration: "28 minutes",
        impact: "Degraded performance",
        failedChecks: 9,
        errorDetails: null,
        updates: [
            {
                timestamp: "4 days ago",
                message:
                    "Database performance degradation detected. Analyzing query logs.",
            },
            {
                timestamp: "4 days ago",
                message:
                    "Identified long-running queries from scheduled backup process.",
            },
            {
                timestamp: "4 days ago",
                message: "Backup process rescheduled. Performance normalized.",
            },
        ],
    },
    {
        id: 7,
        monitor: "CDN Service",
        message: "HTTP 503 Service Unavailable",
        severity: "critical",
        status: "resolved",
        startTime: "5 days ago",
        duration: "3 minutes",
        impact: "Complete outage",
        failedChecks: 6,
        errorDetails: "HTTP 503: Service Temporarily Unavailable",
        updates: [
            {
                timestamp: "5 days ago",
                message:
                    "CDN returning 503 errors. Investigating with provider.",
            },
            {
                timestamp: "5 days ago",
                message:
                    "Provider confirmed brief maintenance window. Service restored.",
            },
        ],
    },
]);

// Computed values
const totalIncidents = computed(() => incidents.value.length);

const criticalCount = computed(() => {
    return incidents.value.filter((i) => i.severity === "critical").length;
});

const averageDowntime = computed(() => {
    // Calculate average in minutes
    const durations = [45, 83, 12, 28, 3]; // example durations in minutes
    const avg = durations.reduce((a, b) => a + b, 0) / durations.length;
    return `${Math.round(avg)}min`;
});

const mttr = computed(() => {
    return "42min"; // Mean time to recovery
});

const filteredIncidents = computed(() => {
    if (activeFilter.value === "all") {
        return incidents.value;
    }
    if (activeFilter.value === "critical" || activeFilter.value === "warning") {
        return incidents.value.filter((i) => i.severity === activeFilter.value);
    }
    return incidents.value.filter((i) => i.status === activeFilter.value);
});
</script>
