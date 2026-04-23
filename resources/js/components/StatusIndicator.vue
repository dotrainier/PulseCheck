<template>
    <div :class="containerClass">
        <div
            v-if="showPing"
            class="rounded-full bg-cyan-400 animate-ping absolute"
            :class="sizeClass"
        ></div>
        <div class="rounded-full" :class="[sizeClass, statusColorClass]"></div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    status: {
        type: String,
        required: true,
        validator: (value) =>
            ["operational", "down", "degraded", "unknown"].includes(value),
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["sm", "md", "lg"].includes(value),
    },
});

const containerClass = computed(() => ({
    "flex items-center justify-center": true,
    relative: props.status === "operational",
}));

const showPing = computed(() => props.status === "operational");

const statusColorClass = computed(() => {
    const colors = {
        operational: "bg-cyan-400",
        down: "bg-red-400",
        degraded: "bg-yellow-400",
        unknown: "bg-gray-400",
    };

    return colors[props.status] || colors.unknown;
});

const sizeClass = computed(() => {
    const sizes = {
        sm: "w-1.5 h-1.5",
        md: "w-2.5 h-2.5",
        lg: "w-3 h-3",
    };
    return sizes[props.size] || sizes.md;
});
</script>
