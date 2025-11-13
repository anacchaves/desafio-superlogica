<template>
    <div class="flex justify-center items-center" :class="containerClass">
        <div
            class="animate-spin rounded-full border-t-2 border-b-2"
            :class="[sizeClass, colorClass]"
        ></div>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";

interface Props {
    size?: "sm" | "md" | "lg";
    color?: "primary" | "white" | "gray";
    fullScreen?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    size: "md",
    color: "primary",
    fullScreen: false,
});

const sizeClass = computed(() => {
    switch (props.size) {
        case "sm":
            return "h-6 w-6 border-2";
        case "lg":
            return "h-16 w-16 border-4";
        default:
            return "h-10 w-10 border-2";
    }
});

const colorClass = computed(() => {
    switch (props.color) {
        case "white":
            return "border-white";
        case "gray":
            return "border-gray-600";
        default:
            return "border-blue-600";
    }
});

const containerClass = computed(() => {
    return props.fullScreen ? "min-h-screen" : "py-8";
});
</script>
