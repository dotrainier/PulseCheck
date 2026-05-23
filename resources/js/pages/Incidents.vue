<template>
    <div class="min-h-screen bg-[#0D0D12] text-white">
        <!-- Header -->
        <div class="border-b border-white/10 bg-[#0D0D12]/90 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <div>
                    <h1
                        class="text-xl sm:text-2xl font-bold tracking-tight"
                        :style="{ fontFamily: 'Cabinet Grotesk, system-ui, sans-serif' }"
                    >
                        Incidents
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-400 mt-0.5 sm:mt-1">Downtime history and incident reports</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-8 space-y-4 sm:space-y-6">
            <!-- Loading -->
            <div v-if="loading" class="flex items-center justify-center py-16">
                <div class="w-8 h-8 border-2 border-cyan-500/30 border-t-cyan-400 rounded-full animate-spin"></div>
            </div>

            <template v-else>
                <!-- Stats Overview -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                    <div class="p-3 sm:p-4 bg-[#16161E] border border-white/10 rounded-lg" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                        <div class="text-xs text-gray-500 mb-1">Total Incidents</div>
                        <div class="text-xl sm:text-2xl font-bold">{{ stats.total }}</div>
                        <div class="text-xs text-gray-400 mt-1">Last 30 days</div>
                    </div>
                    <div class="p-3 sm:p-4 bg-[#16161E] border border-white/10 rounded-lg" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                        <div class="text-xs text-gray-500 mb-1">Critical</div>
                        <div class="text-xl sm:text-2xl font-bold text-red-400">{{ stats.critical }}</div>
                        <div class="text-xs text-gray-400 mt-1">High priority</div>
                    </div>
                    <div class="p-3 sm:p-4 bg-[#16161E] border border-white/10 rounded-lg" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                        <div class="text-xs text-gray-500 mb-1">Avg Downtime</div>
                        <div class="text-xl sm:text-2xl font-bold text-yellow-400">{{ stats.average_downtime }}</div>
                        <div class="text-xs text-gray-400 mt-1">Per incident</div>
                    </div>
                    <div class="p-3 sm:p-4 bg-[#16161E] border border-white/10 rounded-lg" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                        <div class="text-xs text-gray-500 mb-1">MTTR</div>
                        <div class="text-xl sm:text-2xl font-bold text-emerald-400">{{ stats.mttr }}</div>
                        <div class="text-xs text-gray-400 mt-1">Mean time to recovery</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        v-for="f in statusFilters"
                        :key="f.value"
                        @click="activeFilter = f.value"
                        class="px-3 py-1.5 text-sm rounded-lg transition-all"
                        :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                        :class="{
                            'bg-linear-to-r from-cyan-600 to-teal-600 text-white shadow-lg shadow-cyan-500/25': activeFilter === f.value,
                            'bg-[#16161E] text-gray-400 hover:text-white border border-white/10': activeFilter !== f.value,
                        }"
                    >{{ f.label }}</button>
                </div>

                <!-- Incidents Timeline -->
                <div class="bg-[#16161E] border border-white/10 rounded-lg p-4 sm:p-6">
                    <div class="mb-4 sm:mb-6">
                        <h2 class="text-base sm:text-lg font-semibold">Incident History</h2>
                        <p class="text-xs text-gray-500 mt-0.5 sm:mt-1">Chronological list of all incidents</p>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="incident in filteredIncidents"
                            :key="incident.id"
                            class="relative pl-6 sm:pl-8 pb-4 border-l-2"
                            :class="{
                                'border-red-500': incident.severity === 'critical' && incident.status !== 'resolved',
                                'border-yellow-500': incident.severity === 'warning' && incident.status !== 'resolved',
                                'border-green-500': incident.status === 'resolved',
                                'border-gray-500': incident.status === 'investigating' || incident.status === 'identified',
                            }"
                        >
                            <!-- Timeline Dot -->
                            <div class="absolute left-0 -translate-x-1/2 top-0 w-4 h-4 rounded-full border-2"
                                :class="{
                                    'bg-red-500 border-red-500': incident.severity === 'critical' && incident.status !== 'resolved',
                                    'bg-yellow-500 border-yellow-500': incident.severity === 'warning' && incident.status !== 'resolved',
                                    'bg-green-500 border-green-500': incident.status === 'resolved',
                                    'bg-gray-500 border-gray-500': incident.status === 'investigating' || incident.status === 'identified',
                                }"></div>

                            <!-- Card -->
                            <div class="bg-[#0D0D12] rounded-lg p-4 sm:p-5 hover:bg-[#1A1A24] transition-colors">
                                <!-- Header -->
                                <div class="flex items-start justify-between gap-3 sm:gap-4 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                                            <span class="px-2 py-0.5 text-xs rounded-md capitalize font-medium"
                                                :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                                                :class="{
                                                    'bg-red-500/10 text-red-400 border border-red-500/20': incident.severity === 'critical',
                                                    'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20': incident.severity === 'warning',
                                                }">{{ incident.severity }}</span>
                                            <span class="px-2 py-0.5 text-xs rounded-md capitalize font-medium"
                                                :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                                                :class="{
                                                    'bg-green-500/10 text-green-400 border border-green-500/20': incident.status === 'resolved',
                                                    'bg-blue-500/10 text-blue-400 border border-blue-500/20': incident.status === 'investigating',
                                                    'bg-gray-500/10 text-gray-400 border border-gray-500/20': incident.status === 'identified',
                                                }">{{ incident.status }}</span>
                                        </div>
                                        <h3 class="text-lg font-semibold mb-1">{{ incident.monitor?.name ?? '—' }}</h3>
                                        <p class="text-sm text-gray-400">{{ incident.message }}</p>
                                    </div>
                                </div>

                                <!-- Metrics -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-3 mb-4 text-xs" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                                    <div><div class="text-gray-500">Started</div><div class="font-semibold text-white">{{ incident.start_time }}</div></div>
                                    <div><div class="text-gray-500">Duration</div><div class="font-semibold text-white">{{ incident.duration }}</div></div>
                                    <div><div class="text-gray-500">Impact</div><div class="font-semibold text-white">{{ incident.impact ?? '—' }}</div></div>
                                    <div><div class="text-gray-500">Checks Failed</div><div class="font-semibold text-white">{{ incident.failed_checks }}</div></div>
                                </div>

                                <!-- Updates -->
                                <div v-if="incident.updates && incident.updates.length" class="border-t border-white/10 pt-4 space-y-3">
                                    <div class="text-xs font-semibold text-gray-400">UPDATES</div>
                                    <div v-for="(update, idx) in incident.updates" :key="idx" class="flex gap-3">
                                        <div class="shrink-0 w-1.5 h-1.5 rounded-full bg-gray-500 mt-1.5"></div>
                                        <div class="flex-1">
                                            <div class="text-xs text-gray-500 mb-0.5" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                                                {{ timeAgo(update.created_at) }}
                                            </div>
                                            <div class="text-sm text-gray-300">{{ update.message }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Error Details -->
                                <div v-if="incident.error_details" class="mt-4 p-3 bg-[#16161E] rounded-lg border border-white/10">
                                    <div class="text-xs font-semibold text-gray-400 mb-2">ERROR DETAILS</div>
                                    <code class="text-xs text-red-400 block overflow-x-auto" :style="{ fontFamily: 'JetBrains Mono, monospace' }">
                                        {{ incident.error_details }}
                                    </code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="filteredIncidents.length === 0" class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-linear-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center">
                            <CheckCircleIcon class="w-8 h-8 text-emerald-400" />
                        </div>
                        <h3 class="text-lg font-semibold mb-2">No incidents found</h3>
                        <p class="text-gray-400">
                            {{ activeFilter === 'all' ? 'All systems are running smoothly' : `No ${activeFilter} incidents to display` }}
                        </p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { CheckCircleIcon } from "@heroicons/vue/24/solid";
import api from "@/utils/axios";

const loading = ref(true);
const allIncidents = ref([]);
const stats = ref({ total: 0, critical: 0, average_downtime: "N/A", mttr: "N/A" });
const activeFilter = ref("all");

const statusFilters = [
    { label: "All", value: "all" },
    { label: "Critical", value: "critical" },
    { label: "Warning", value: "warning" },
    { label: "Resolved", value: "resolved" },
    { label: "Investigating", value: "investigating" },
];

const timeAgo = (dateStr) => {
    if (!dateStr) return "—";
    const diff = Date.now() - new Date(dateStr).getTime();
    const m = Math.floor(diff / 60000);
    if (m < 1) return "Just now";
    if (m < 60) return `${m} min ago`;
    const h = Math.floor(m / 60);
    if (h < 24) return `${h}h ago`;
    return `${Math.floor(h / 24)}d ago`;
};

const filteredIncidents = computed(() => {
    if (activeFilter.value === "all") return allIncidents.value;
    if (activeFilter.value === "critical" || activeFilter.value === "warning") {
        return allIncidents.value.filter((i) => i.severity === activeFilter.value);
    }
    return allIncidents.value.filter((i) => i.status === activeFilter.value);
});

const fetchIncidents = async () => {
    loading.value = true;
    try {
        const { data } = await api.get("/api/incidents");
        allIncidents.value = data.data ?? [];
        stats.value = data.stats ?? { total: 0, critical: 0, average_downtime: "N/A", mttr: "N/A" };
    } catch {
        allIncidents.value = [];
    } finally {
        loading.value = false;
    }
};

onMounted(fetchIncidents);
</script>
