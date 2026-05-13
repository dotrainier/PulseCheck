import { storeToRefs } from "pinia";
import { useMonitorsStore } from "@/stores/monitors";

export function useMonitors() {
    const store = useMonitorsStore();
    const {
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
    } = storeToRefs(store);

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
        fetchMonitors: store.fetchMonitors,
        createMonitor: store.createMonitor,
        updateMonitor: store.updateMonitor,
        deleteMonitor: store.deleteMonitor,
        runCheck: store.runCheck,
    };
}
