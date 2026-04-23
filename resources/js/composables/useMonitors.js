// composables/useMonitors.js
import { ref, computed } from "vue";

export function useMonitors() {
    // Static monitor data (in real app, this would fetch from API)
    const monitors = ref([
        {
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
        },
        {
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
        },
        {
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
        },
        {
            id: 4,
            name: "Auth Service",
            url: "https://auth.example.com/ping",
            status: "degraded",
            uptime: 99.97,
            responseTime: 165,
            checkInterval: "30s",
            lastCheck: "1 min ago",
            trackSsl: true,
            sslExpiring: false,
            expectedStatusCode: "200",
            timeout: 30,
        },
        {
            id: 5,
            name: "Database Health",
            url: "https://db.example.com/health",
            status: "operational",
            uptime: 99.99,
            responseTime: 28,
            checkInterval: "1m",
            lastCheck: "30 sec ago",
            trackSsl: true,
            sslExpiring: false,
            expectedStatusCode: "200",
            timeout: 30,
        },
        {
            id: 6,
            name: "Legacy API",
            url: "http://legacy.example.com/api",
            status: "down",
            uptime: 98.5,
            responseTime: 0,
            checkInterval: "5m",
            lastCheck: "5 min ago",
            trackSsl: false,
            sslExpiring: false,
            expectedStatusCode: "200",
            timeout: 30,
        },
    ]);

    // Computed statistics
    const totalMonitors = computed(() => monitors.value.length);

    const operationalCount = computed(() => {
        return monitors.value.filter((m) => m.status === "operational").length;
    });

    const downCount = computed(() => {
        return monitors.value.filter((m) => m.status === "down").length;
    });

    const degradedCount = computed(() => {
        return monitors.value.filter((m) => m.status === "degraded").length;
    });

    const systemStatus = computed(() => {
        if (downCount.value > 0) return "down";
        if (degradedCount.value > 0) return "degraded";
        return "operational";
    });

    const averageUptime = computed(() => {
        const total = monitors.value.reduce((sum, m) => sum + m.uptime, 0);
        return (total / monitors.value.length).toFixed(2);
    });

    const averageResponseTime = computed(() => {
        const activeMonitors = monitors.value.filter(
            (m) => m.status !== "down",
        );
        if (activeMonitors.length === 0) return 0;
        const total = activeMonitors.reduce(
            (sum, m) => sum + m.responseTime,
            0,
        );
        return Math.round(total / activeMonitors.length);
    });

    return {
        monitors,
        totalMonitors,
        operationalCount,
        downCount,
        degradedCount,
        systemStatus,
        averageUptime,
        averageResponseTime,
    };
}
