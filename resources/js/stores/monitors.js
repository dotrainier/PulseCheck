import { defineStore } from "pinia";
import { ref, computed } from "vue";
import api from "@/utils/axios";

export const useMonitorsStore = defineStore("monitors", () => {
    const monitors = ref([]);
    const loading = ref(false);
    const error = ref(null);

    const totalMonitors = computed(() => monitors.value.length);
    const operationalCount = computed(
        () => monitors.value.filter((m) => m.status === "operational").length,
    );
    const downCount = computed(
        () => monitors.value.filter((m) => m.status === "down").length,
    );
    const degradedCount = computed(
        () => monitors.value.filter((m) => m.status === "degraded").length,
    );
    const systemStatus = computed(() => {
        if (downCount.value > 0) return "down";
        if (degradedCount.value > 0) return "degraded";
        return "operational";
    });
    const averageUptime = computed(() => {
        if (!monitors.value.length) return 100;
        const sum = monitors.value.reduce(
            (acc, m) => acc + parseFloat(m.uptime || 0),
            0,
        );
        return (sum / monitors.value.length).toFixed(2);
    });
    const averageResponseTime = computed(() => {
        const active = monitors.value.filter(
            (m) => m.avg_response_time != null,
        );
        if (!active.length) return 0;
        return Math.round(
            active.reduce((acc, m) => acc + m.avg_response_time, 0) /
                active.length,
        );
    });

    const fetchMonitors = async () => {
        loading.value = true;
        error.value = null;
        try {
            const { data } = await api.get("/api/monitors");
            monitors.value = data.data;
        } catch (e) {
            error.value = e.response?.data?.message || "Failed to load monitors";
        } finally {
            loading.value = false;
        }
    };

    const createMonitor = async (formData) => {
        const { data } = await api.post("/api/monitors", formData);
        monitors.value.push(data.data);
        return data.data;
    };

    const updateMonitor = async (id, formData) => {
        const { data } = await api.put(`/api/monitors/${id}`, formData);
        const index = monitors.value.findIndex((m) => m.id === id);
        if (index !== -1) monitors.value[index] = data.data;
        return data.data;
    };

    const deleteMonitor = async (id) => {
        await api.delete(`/api/monitors/${id}`);
        monitors.value = monitors.value.filter((m) => m.id !== id);
    };

    const runCheck = async (id) => {
        const { data } = await api.post(`/api/monitors/${id}/check`);
        const index = monitors.value.findIndex((m) => m.id === id);
        if (index !== -1) monitors.value[index] = data.monitor;
        return data;
    };

    return {
        monitors,
        loading,
        error,
        totalMonitors,
        operationalCount,
        downCount,
        degradedCount,
        systemStatus,
        averageUptime,
        averageResponseTime,
        fetchMonitors,
        createMonitor,
        updateMonitor,
        deleteMonitor,
        runCheck,
    };
});
