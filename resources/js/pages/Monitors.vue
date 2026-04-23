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
                            Monitors
                        </h1>
                        <p class="text-sm text-gray-400 mt-1">
                            {{ monitors.length }} active monitors •
                            {{ operationalCount }} operational •
                            {{ downCount }} down
                        </p>
                    </div>
                    <button
                        @click="openAddModal"
                        class="px-4 py-2 bg-gradient-to-r from-cyan-600 to-teal-600 text-sm font-medium rounded-lg hover:from-cyan-500 hover:to-teal-500 transition-all shadow-lg shadow-cyan-500/25 flex items-center gap-2"
                    >
                        <PlusIcon class="w-4 h-4" />
                        <span>Add Monitor</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Monitors List -->
            <div class="space-y-3">
                <div
                    v-for="monitor in monitors"
                    :key="monitor.id"
                    class="group p-5 bg-[#16161E] border border-white/10 rounded-lg hover:border-cyan-500/30 transition-all cursor-pointer"
                    @click="viewMonitor(monitor)"
                >
                    <div class="flex items-start justify-between gap-4">
                        <!-- Left: Status & Info -->
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            <!-- Status Indicator -->
                            <div class="flex-shrink-0 mt-1">
                                <StatusIndicator :status="monitor.status" />
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-base font-semibold">
                                        {{ monitor.name }}
                                    </h3>
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-md capitalize"
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
                                    <!-- SSL Warning Badge -->
                                    <span
                                        v-if="
                                            monitor.trackSsl &&
                                            monitor.sslExpiring
                                        "
                                        class="px-2 py-0.5 text-xs rounded-md bg-orange-500/10 text-orange-400 border border-orange-500/20 flex items-center gap-1"
                                        :style="{
                                            fontFamily:
                                                'JetBrains Mono, monospace',
                                        }"
                                    >
                                        <ExclamationTriangleIcon
                                            class="w-3 h-3"
                                        />
                                        SSL Expiring
                                    </span>
                                </div>
                                <div
                                    class="text-sm text-gray-400 truncate mb-2"
                                    :style="{
                                        fontFamily: 'JetBrains Mono, monospace',
                                    }"
                                >
                                    {{ monitor.url }}
                                </div>

                                <!-- Metrics Grid -->
                                <div
                                    class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs"
                                    :style="{
                                        fontFamily: 'JetBrains Mono, monospace',
                                    }"
                                >
                                    <div>
                                        <div class="text-gray-500">Uptime</div>
                                        <div class="font-semibold text-white">
                                            {{ monitor.uptime }}%
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">
                                            Response Time
                                        </div>
                                        <div class="font-semibold text-white">
                                            {{ monitor.responseTime }}ms
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">
                                            Interval
                                        </div>
                                        <div class="font-semibold text-white">
                                            {{ monitor.checkInterval }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">
                                            Last Check
                                        </div>
                                        <div class="font-semibold text-white">
                                            {{ monitor.lastCheck }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Actions -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button
                                @click.stop="editMonitor(monitor)"
                                class="p-2 hover:bg-white/5 rounded-lg transition-colors"
                                title="Edit"
                            >
                                <PencilIcon class="w-5 h-5 text-gray-400" />
                            </button>
                            <button
                                @click.stop="deleteMonitor(monitor)"
                                class="p-2 hover:bg-red-500/10 rounded-lg transition-colors"
                                title="Delete"
                            >
                                <TrashIcon class="w-5 h-5 text-gray-400" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="monitors.length === 0"
                class="text-center py-16 bg-[#16161E] border border-white/10 rounded-lg"
            >
                <div
                    class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-cyan-500/20 to-teal-500/20 flex items-center justify-center"
                >
                    <ChartBarIcon class="w-8 h-8 text-cyan-400" />
                </div>
                <h3 class="text-lg font-semibold mb-2">No monitors yet</h3>
                <p class="text-gray-400 mb-4">
                    Get started by adding your first monitor
                </p>
                <button
                    @click="openAddModal"
                    class="px-4 py-2 bg-gradient-to-r from-cyan-600 to-teal-600 text-sm font-medium rounded-lg hover:from-cyan-500 hover:to-teal-500 transition-all shadow-lg shadow-cyan-500/25"
                >
                    Add Monitor
                </button>
            </div>
        </div>

        <!-- Add/Edit Monitor Modal -->
        <Teleport to="body">
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                @click.self="closeModal"
            >
                <div
                    class="w-full max-w-2xl bg-[#16161E] border border-white/10 rounded-xl shadow-2xl"
                >
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-white/10">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold">
                                {{ isEditing ? "Edit Monitor" : "Add Monitor" }}
                            </h2>
                            <button
                                @click="closeModal"
                                class="p-1 hover:bg-white/5 rounded-lg transition-colors"
                            >
                                <XMarkIcon class="w-5 h-5 text-gray-400" />
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div
                        class="px-6 py-6 space-y-5 max-h-[70vh] overflow-y-auto"
                    >
                        <!-- Name -->
                        <div>
                            <label
                                class="block text-sm font-medium mb-2 text-gray-300"
                                >Name</label
                            >
                            <input
                                v-model="formData.name"
                                type="text"
                                placeholder="My Website"
                                class="w-full px-4 py-2 bg-[#0D0D12] border border-white/10 rounded-lg focus:border-cyan-500 focus:outline-none transition-colors"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            />
                        </div>

                        <!-- URL -->
                        <div>
                            <label
                                class="block text-sm font-medium mb-2 text-gray-300"
                                >URL</label
                            >
                            <input
                                v-model="formData.url"
                                type="text"
                                placeholder="https://example.com"
                                class="w-full px-4 py-2 bg-[#0D0D12] border border-white/10 rounded-lg focus:border-cyan-500 focus:outline-none transition-colors"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            />
                        </div>

                        <!-- Check Interval -->
                        <div>
                            <label
                                class="block text-sm font-medium mb-2 text-gray-300"
                                >Check Interval</label
                            >
                            <select
                                v-model="formData.checkInterval"
                                class="w-full px-4 py-2 bg-[#0D0D12] border border-white/10 rounded-lg focus:border-cyan-500 focus:outline-none transition-colors"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            >
                                <option value="30s">30 seconds</option>
                                <option value="1m">1 minute</option>
                                <option value="5m">5 minutes</option>
                                <option value="1h">1 hour</option>
                                <option value="6h">6 hours</option>
                                <option value="24h">24 hours</option>
                            </select>
                        </div>

                        <!-- Expected Status Code -->
                        <div>
                            <label
                                class="block text-sm font-medium mb-2 text-gray-300"
                                >Expected Status Code
                                <span class="text-gray-500">(optional)</span>
                            </label>
                            <input
                                v-model="formData.expectedStatusCode"
                                type="text"
                                placeholder="200"
                                class="w-full px-4 py-2 bg-[#0D0D12] border border-white/10 rounded-lg focus:border-cyan-500 focus:outline-none transition-colors"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            />
                        </div>

                        <!-- Timeout -->
                        <div>
                            <label
                                class="block text-sm font-medium mb-2 text-gray-300"
                                >Timeout (seconds)
                                <span class="text-gray-500">(optional)</span>
                            </label>
                            <input
                                v-model="formData.timeout"
                                type="number"
                                placeholder="30"
                                class="w-full px-4 py-2 bg-[#0D0D12] border border-white/10 rounded-lg focus:border-cyan-500 focus:outline-none transition-colors"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            />
                        </div>

                        <!-- Track SSL -->
                        <div>
                            <label
                                class="flex items-center gap-3 cursor-pointer"
                            >
                                <input
                                    v-model="formData.trackSsl"
                                    type="checkbox"
                                    class="w-4 h-4 rounded border-white/10 bg-[#0D0D12] text-cyan-500 focus:ring-cyan-500 focus:ring-offset-0"
                                />
                                <div>
                                    <div
                                        class="text-sm font-medium text-gray-300"
                                    >
                                        Track SSL Certificate
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Monitor SSL certificate expiration
                                        (auto-enabled for HTTPS URLs)
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="px-6 py-4 border-t border-white/10 flex items-center justify-end gap-3"
                    >
                        <button
                            @click="closeModal"
                            class="px-4 py-2 text-sm font-medium hover:bg-white/5 rounded-lg transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="saveMonitor"
                            class="px-4 py-2 bg-gradient-to-r from-cyan-600 to-teal-600 text-sm font-medium rounded-lg hover:from-cyan-500 hover:to-teal-500 transition-all shadow-lg shadow-cyan-500/25"
                        >
                            {{ isEditing ? "Save Changes" : "Add Monitor" }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import {
    PlusIcon,
    PencilIcon,
    TrashIcon,
    XMarkIcon,
    ChartBarIcon,
    ExclamationTriangleIcon,
} from "@heroicons/vue/24/solid";
import { useMonitors } from "@/composables/useMonitors";
import StatusIndicator from "@/components/StatusIndicator.vue";

const router = useRouter();

// Use shared monitor data
const { monitors, operationalCount, downCount } = useMonitors();

// Modal state
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

// Form data
const formData = ref({
    name: "",
    url: "",
    checkInterval: "1m",
    expectedStatusCode: "",
    timeout: "",
    trackSsl: false,
});

// Modal functions
const openAddModal = () => {
    isEditing.value = false;
    formData.value = {
        name: "",
        url: "",
        checkInterval: "1m",
        expectedStatusCode: "",
        timeout: "",
        trackSsl: false,
    };
    showModal.value = true;
};

const editMonitor = (monitor) => {
    isEditing.value = true;
    editingId.value = monitor.id;
    formData.value = {
        name: monitor.name,
        url: monitor.url,
        checkInterval: monitor.checkInterval,
        expectedStatusCode: monitor.expectedStatusCode,
        timeout: monitor.timeout,
        trackSsl: monitor.trackSsl,
    };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    isEditing.value = false;
    editingId.value = null;
};

const saveMonitor = () => {
    // In real app, this would call API
    console.log("Saving monitor:", formData.value);
    closeModal();
};

const deleteMonitor = (monitor) => {
    if (confirm(`Are you sure you want to delete "${monitor.name}"?`)) {
        console.log("Deleting monitor:", monitor.id);
        // In real app, this would call API
    }
};

const viewMonitor = (monitor) => {
    router.push(`/monitors/${monitor.id}`);
};
</script>
