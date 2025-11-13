<template>
    <div class="container mx-auto px-4 py-8">
        <!-- Loading State -->
        <LoadingSpinner v-if="loading" size="lg" />

        <!-- Error State (404) -->
        <div
            v-else-if="notFound"
            class="bg-white rounded-lg shadow p-12 text-center"
        >
            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                Produto não encontrado
            </h2>
            <p class="text-gray-600 mb-6">
                O produto que você está procurando não existe ou foi removido.
            </p>
            <router-link
                to="/products"
                class="inline-block bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700"
            >
                Voltar para Lista
            </router-link>
        </div>

        <!-- Product Details -->
        <div v-else-if="product" class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    Detalhes do Produto
                </h1>
                <router-link
                    to="/products"
                    class="text-gray-600 hover:text-gray-900 flex items-center"
                >
                    <svg
                        class="w-5 h-5 mr-1"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>
                    Voltar para Lista
                </router-link>
            </div>

            <!-- Product Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                                {{ product.name }}
                            </h2>
                            <span
                                :class="[
                                    'px-3 py-1 inline-flex text-sm font-semibold rounded-full',
                                    product.is_active
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800',
                                ]"
                            >
                                {{ product.is_active ? "Ativo" : "Inativo" }}
                            </span>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-blue-600">
                                R$ {{ formatPrice(product.price) }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">
                                Descrição
                            </h3>
                            <p class="text-gray-900">
                                {{ product.description || "Sem descrição" }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">
                                Estoque
                            </h3>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ product.stock }} unidades
                            </p>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-200"
                    >
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">
                                Criado em
                            </h3>
                            <p class="text-gray-900">
                                {{ formatDate(product.created_at) }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">
                                Atualizado em
                            </h3>
                            <p class="text-gray-900">
                                {{ formatDate(product.updated_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                    <button
                        @click="handleDelete"
                        class="px-4 py-2 border border-red-300 text-red-700 rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                        Excluir
                    </button>
                    <router-link
                        :to="`/products/${product.id}/edit`"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Editar
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog
            :is-open="showDeleteConfirm"
            title="Confirmar Exclusão"
            message="Tem certeza que deseja excluir este produto?"
            confirm-text="Excluir"
            cancel-text="Cancelar"
            variant="danger"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useApi } from "../composables/useApi";
import { useNotification } from "../composables/useNotification";
import { useErrorHandler } from "../composables/useErrorHandler";
import { ApiError } from "../types/errors";
import LoadingSpinner from "../components/LoadingSpinner.vue";
import ConfirmDialog from "../components/ConfirmDialog.vue";

interface Product {
    id: number;
    name: string;
    description: string | null;
    price: string;
    stock: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

const route = useRoute();
const router = useRouter();
const api = useApi();
const { showSuccess } = useNotification();
const { handleError } = useErrorHandler();

const product = ref<Product | null>(null);
const loading = ref(false);
const notFound = ref(false);
const showDeleteConfirm = ref(false);

async function fetchProduct() {
    loading.value = true;
    notFound.value = false;

    try {
        const productId = route.params.id;
        const response = await api.get(`/products/${productId}`);

        if (response.success && response.data) {
            product.value = response.data;
        }
    } catch (error) {
        if (error instanceof ApiError && error.statusCode === 404) {
            notFound.value = true;
            setTimeout(() => {
                router.push("/products");
            }, 2000);
        } else {
            handleError(error, "carregar produto");
        }
    } finally {
        loading.value = false;
    }
}

function formatPrice(price: string): string {
    const num = parseFloat(price);
    return num.toFixed(2).replace(".", ",");
}

function formatDate(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleDateString("pt-BR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function handleDelete() {
    showDeleteConfirm.value = true;
}

function cancelDelete() {
    showDeleteConfirm.value = false;
}

async function confirmDelete() {
    if (!product.value) return;

    showDeleteConfirm.value = false;

    try {
        const response = await api.delete(`/products/${product.value.id}`);

        if (response.success) {
            showSuccess(response.message || "Produto excluído com sucesso");
            router.push("/products");
        }
    } catch (error) {
        handleError(error, "excluir produto");
    }
}

onMounted(() => {
    fetchProduct();
});
</script>
