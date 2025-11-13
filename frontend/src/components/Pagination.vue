<template>
    <div
        v-if="totalPages > 1"
        class="flex items-center justify-center space-x-2 py-4"
    >
        <!-- Previous Button -->
        <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
            :class="
                currentPage === 1
                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                    : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
            "
        >
            Anterior
        </button>

        <!-- Page Numbers -->
        <div class="flex items-center space-x-1">
            <button
                v-for="page in visiblePages"
                :key="page"
                @click="goToPage(page)"
                class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                :class="
                    page === currentPage
                        ? 'bg-blue-600 text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                "
            >
                {{ page }}
            </button>
        </div>

        <!-- Next Button -->
        <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
            :class="
                currentPage === totalPages
                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                    : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
            "
        >
            Próxima
        </button>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";

interface Props {
    currentPage: number;
    totalPages: number;
    maxVisiblePages?: number;
}

interface Emits {
    (e: "change", page: number): void;
}

const props = withDefaults(defineProps<Props>(), {
    maxVisiblePages: 5,
});

const emit = defineEmits<Emits>();

const visiblePages = computed(() => {
    const pages: number[] = [];
    const half = Math.floor(props.maxVisiblePages / 2);

    let start = Math.max(1, props.currentPage - half);
    let end = Math.min(props.totalPages, start + props.maxVisiblePages - 1);

    // Adjust start if we're near the end
    if (end - start + 1 < props.maxVisiblePages) {
        start = Math.max(1, end - props.maxVisiblePages + 1);
    }

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }

    return pages;
});

function goToPage(page: number) {
    if (page >= 1 && page <= props.totalPages && page !== props.currentPage) {
        emit("change", page);
    }
}
</script>
