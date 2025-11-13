<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="isOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                @click.self="handleCancel"
            >
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black bg-opacity-50"></div>

                <!-- Modal -->
                <div
                    class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-10"
                    @click.stop
                >
                    <!-- Title -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        {{ title }}
                    </h3>

                    <!-- Message -->
                    <p class="text-gray-600 mb-6">
                        {{ message }}
                    </p>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="handleCancel"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                        >
                            {{ cancelText }}
                        </button>
                        <button
                            @click="handleConfirm"
                            class="px-4 py-2 text-sm font-medium text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
                            :class="confirmButtonClass"
                        >
                            {{ confirmText }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
import { computed } from "vue";

interface Props {
    isOpen: boolean;
    title: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    variant?: "danger" | "primary";
}

interface Emits {
    (e: "confirm"): void;
    (e: "cancel"): void;
}

const props = withDefaults(defineProps<Props>(), {
    confirmText: "Confirmar",
    cancelText: "Cancelar",
    variant: "danger",
});

const emit = defineEmits<Emits>();

const confirmButtonClass = computed(() => {
    return props.variant === "danger"
        ? "bg-red-600 hover:bg-red-700 focus:ring-red-500"
        : "bg-blue-600 hover:bg-blue-700 focus:ring-blue-500";
});

function handleConfirm() {
    emit("confirm");
}

function handleCancel() {
    emit("cancel");
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.2s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.95);
}
</style>
