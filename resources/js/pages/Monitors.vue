<template>
    <div class="min-h-screen bg-[#070D1A] text-white">
        <!-- Header -->
        <div class="border-b border-white/8 bg-[#070D1A]/90 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h1
                            class="text-xl sm:text-2xl font-bold tracking-tight"
                            :style="{
                                fontFamily:
                                    'Cabinet Grotesk, system-ui, sans-serif',
                            }"
                        >
                            Monitors
                        </h1>
                        <p class="text-xs sm:text-sm text-gray-400 mt-0.5 sm:mt-1">
                            {{ totalMonitors }} monitors &bull;
                            {{ operationalCount }} up &bull;
                            {{ downCount }} down
                        </p>
                    </div>
                    <button
                        @click="openAddModal"
                        class="px-3 sm:px-4 py-2 sm:py-2 bg-amber-500 text-black text-sm font-medium rounded-lg hover:bg-amber-400 transition-all shadow-lg shadow-amber-500/25 flex items-center gap-2 shrink-0"
                    >
                        <PlusIcon class="w-4 h-4" />
                        <span class="hidden sm:inline">Add Monitor</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-8">
            <!-- Loading -->
            <div v-if="loading" class="flex items-center justify-center py-16">
                <div
                    class="w-8 h-8 border-2 border-amber-500/30 border-t-amber-400 rounded-full animate-spin"
                ></div>
            </div>

            <!-- Error -->
            <div
                v-else-if="error"
                class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-sm"
            >
                {{ error }}
            </div>

            <!-- Monitors List -->
            <div v-else class="space-y-3">
                <div
                    v-for="monitor in monitors"
                    :key="monitor.id"
                    class="group p-4 sm:p-5 bg-[#0D1828] border border-white/8 rounded-lg hover:border-amber-500/25 transition-all cursor-pointer"
                    @click="viewMonitor(monitor)"
                >
                    <div class="flex items-start justify-between gap-3 sm:gap-4">
                        <div class="flex items-start gap-3 sm:gap-4 flex-1 min-w-0">
                            <div class="shrink-0 mt-1">
                                <StatusIndicator :status="monitor.status" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div
                                    class="flex items-center gap-2 mb-1 flex-wrap"
                                >
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
                                            'bg-green-500/10 text-green-400 border border-green-500/20':
                                                monitor.status ===
                                                'operational',
                                            'bg-red-500/10 text-red-400 border border-red-500/20':
                                                monitor.status === 'down',
                                            'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20':
                                                monitor.status === 'degraded',
                                            'bg-gray-500/10 text-gray-400 border border-gray-500/20':
                                                monitor.status === 'pending',
                                        }"
                                        >{{ monitor.status }}</span
                                    >
                                    <span
                                        v-if="
                                            monitor.track_ssl &&
                                            monitor.ssl_expiring
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
                                            {{
                                                monitor.avg_response_time
                                                    ? `${monitor.avg_response_time}ms`
                                                    : "—"
                                            }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">
                                            Interval
                                        </div>
                                        <div class="font-semibold text-white">
                                            {{ monitor.check_interval }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">
                                            Last Check
                                        </div>
                                        <div class="font-semibold text-white">
                                            {{
                                                timeAgo(monitor.last_checked_at)
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                            <button
                                @click.stop="runCheckNow(monitor)"
                                :disabled="checking === monitor.id"
                                class="p-2 hover:bg-amber-500/10 rounded-lg transition-colors min-w-9 flex items-center justify-center"
                                title="Run check now"
                            >
                                <ArrowPathIcon
                                    class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400"
                                    :class="{
                                        'animate-spin': checking === monitor.id,
                                    }"
                                />
                            </button>
                            <button
                                @click.stop="openEditModal(monitor)"
                                class="p-2 hover:bg-white/5 rounded-lg transition-colors min-w-9 flex items-center justify-center"
                                title="Edit"
                            >
                                <PencilIcon class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" />
                            </button>
                            <button
                                @click.stop="confirmDelete(monitor)"
                                class="p-2 hover:bg-red-500/10 rounded-lg transition-colors min-w-9 flex items-center justify-center"
                                title="Delete"
                            >
                                <TrashIcon class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="!loading && !error && monitors.length === 0"
                class="text-center py-16 bg-[#0D1828] border border-white/8 rounded-lg"
            >
                <div
                    class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-500/10 border border-amber-500/20 flex items-center justify-center"
                >
                    <ChartBarIcon class="w-8 h-8 text-amber-400" />
                </div>
                <h3 class="text-lg font-semibold mb-2">No monitors yet</h3>
                <p class="text-gray-400 mb-4">
                    Get started by adding your first monitor
                </p>
                <button
                    @click="openAddModal"
                    class="px-4 py-2 bg-amber-500 text-black text-sm font-medium rounded-lg hover:bg-amber-400 transition-all shadow-lg shadow-amber-500/25"
                >
                    Add Monitor
                </button>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Teleport to="body">
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                @click.self="closeModal"
            >
                <div
                    class="w-full max-w-2xl bg-[#0D1828] border border-white/8 rounded-xl shadow-2xl"
                >
                    <div
                        class="px-6 py-4 border-b border-white/8 flex items-center justify-between"
                    >
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

                    <div
                        class="px-4 sm:px-6 py-4 sm:py-6 space-y-4 sm:space-y-5 max-h-[60vh] sm:max-h-[70vh] overflow-y-auto"
                    >
                        <div
                            v-if="formError"
                            class="p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-sm text-red-400"
                        >
                            {{ formError }}
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium mb-2 text-gray-300"
                                >Name</label
                            >
                            <input
                                v-model="formData.name"
                                type="text"
                                placeholder="My Website"
                                class="w-full px-4 py-2 bg-[#070D1A] border border-white/8 rounded-lg focus:border-amber-500/50 focus:outline-none focus:ring-1 focus:ring-amber-500/20 transition-colors"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            />
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium mb-2 text-gray-300"
                                >URL</label
                            >
                            <input
                                v-model="formData.url"
                                type="text"
                                placeholder="https://example.com"
                                class="w-full px-4 py-2 bg-[#070D1A] border border-white/8 rounded-lg focus:border-amber-500/50 focus:outline-none focus:ring-1 focus:ring-amber-500/20 transition-colors"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            />
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium mb-2 text-gray-300"
                                >Check Interval</label
                            >
                            <select
                                v-model="formData.check_interval"
                                class="w-full px-4 py-2 bg-[#070D1A] border border-white/8 rounded-lg focus:border-amber-500/50 focus:outline-none focus:ring-1 focus:ring-amber-500/20 transition-colors"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            >
                                <option value="30s">30 seconds</option>
                                <option value="1m">1 minute</option>
                                <option value="5m">5 minutes</option>
                                <option value="15m">15 minutes</option>
                                <option value="30m">30 minutes</option>
                                <option value="1h">1 hour</option>
                                <option value="6h">6 hours</option>
                                <option value="24h">24 hours</option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium mb-2 text-gray-300"
                            >
                                Expected Status Code
                                <span class="text-gray-500">(optional)</span>
                            </label>
                            <input
                                v-model="formData.expected_status_code"
                                type="text"
                                placeholder="200"
                                class="w-full px-4 py-2 bg-[#070D1A] border border-white/8 rounded-lg focus:border-amber-500/50 focus:outline-none focus:ring-1 focus:ring-amber-500/20 transition-colors"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            />
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium mb-2 text-gray-300"
                            >
                                Timeout (seconds)
                                <span class="text-gray-500">(optional)</span>
                            </label>
                            <input
                                v-model="formData.timeout"
                                type="number"
                                placeholder="30"
                                class="w-full px-4 py-2 bg-[#070D1A] border border-white/8 rounded-lg focus:border-amber-500/50 focus:outline-none focus:ring-1 focus:ring-amber-500/20 transition-colors"
                                :style="{
                                    fontFamily: 'JetBrains Mono, monospace',
                                }"
                            />
                        </div>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input
                                v-model="formData.track_ssl"
                                type="checkbox"
                                class="w-4 h-4 rounded border-white/8 bg-[#070D1A] text-amber-500 focus:ring-amber-500 focus:ring-offset-0"
                            />
                            <div>
                                <div class="text-sm font-medium text-gray-300">
                                    Track SSL Certificate
                                </div>
                                <div class="text-xs text-gray-500">
                                    Monitor SSL certificate expiration
                                </div>
                            </div>
                        </label>
                    </div>

                    <div
                        class="px-6 py-4 border-t border-white/8 flex items-center justify-end gap-3"
                    >
                        <button
                            @click="closeModal"
                            class="px-4 py-2 text-sm font-medium hover:bg-white/5 rounded-lg transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="saveMonitor"
                            :disabled="saving"
                            class="px-4 py-2 bg-amber-500 text-black text-sm font-medium rounded-lg hover:bg-amber-400 transition-all shadow-lg shadow-amber-500/25 disabled:opacity-50 flex items-center gap-2"
                        >
                            <div
                                v-if="saving"
                                class="w-4 h-4 border-2 border-black/30 border-t-black rounded-full animate-spin"
                            ></div>
                            {{ isEditing ? "Save Changes" : "Add Monitor" }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import {
    PlusIcon,
    PencilIcon,
    TrashIcon,
    XMarkIcon,
    ChartBarIcon,
    ExclamationTriangleIcon,
    ArrowPathIcon,
} from "@heroicons/vue/24/solid";
import { useMonitors } from "@/composables/useMonitors";
import StatusIndicator from "@/components/StatusIndicator.vue";
import { timeAgo } from "@/utils/timeAgo";

const router = useRouter();
const {
    monitors,
    loading,
    error,
    totalMonitors,
    operationalCount,
    downCount,
    fetchMonitors,
    createMonitor,
    updateMonitor,
    deleteMonitor,
    runCheck,
} = useMonitors();

const checking = ref(null);

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const saving = ref(false);
const formError = ref("");

const defaultForm = () => ({
    name: "",
    url: "",
    check_interval: "1m",
    expected_status_code: "",
    timeout: "",
    track_ssl: false,
});

const formData = ref(defaultForm());

const openAddModal = () => {
    isEditing.value = false;
    editingId.value = null;
    formData.value = defaultForm();
    formError.value = "";
    showModal.value = true;
};

const openEditModal = (monitor) => {
    isEditing.value = true;
    editingId.value = monitor.id;
    formData.value = {
        name: monitor.name,
        url: monitor.url,
        check_interval: monitor.check_interval,
        expected_status_code: monitor.expected_status_code ?? "",
        timeout: monitor.timeout ?? "",
        track_ssl: monitor.track_ssl,
    };
    formError.value = "";
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveMonitor = async () => {
    if (!formData.value.name || !formData.value.url) {
        formError.value = "Name and URL are required.";
        return;
    }
    saving.value = true;
    formError.value = "";
    try {
        const payload = {
            ...formData.value,
            expected_status_code: formData.value.expected_status_code || null,
            timeout: formData.value.timeout || null,
        };
        if (isEditing.value) {
            await updateMonitor(editingId.value, payload);
        } else {
            await createMonitor(payload);
        }
        closeModal();
    } catch (e) {
        formError.value =
            e.response?.data?.message || "Failed to save monitor.";
    } finally {
        saving.value = false;
    }
};

const confirmDelete = async (monitor) => {
    if (
        confirm(
            `Delete "${monitor.name}"? This will also remove all check history and incidents.`,
        )
    ) {
        try {
            await deleteMonitor(monitor.id);
        } catch {
            alert("Failed to delete monitor.");
        }
    }
};

const runCheckNow = async (monitor) => {
    checking.value = monitor.id;
    try {
        await runCheck(monitor.id);
    } catch {
        // silently fail
    } finally {
        checking.value = null;
    }
};

const viewMonitor = (monitor) => router.push(`/monitors/${monitor.id}`);

onMounted(fetchMonitors);
</script>
