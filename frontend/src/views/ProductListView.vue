<template>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Produtos</h1>
            <router-link
                to="/products/new"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                Criar Produto
            </router-link>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label
                        for="search"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Buscar
                    </label>
                    <input
                        id="search"
                        v-model="searchTerm"
                        type="text"
                        placeholder="Buscar por nome ou descrição..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <label
                        for="status"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Status
                    </label>
                    <select
                        id="status"
                        v-model="statusFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Todos</option>
                        <option value="1">Ativos</option>
                        <option value="0">Inativos</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <LoadingSpinner v-if="loading" size="lg" />

        <!-- Error Message with Retry -->
        <div
            v-else-if="errorMessage"
            class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6"
        >
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg
                        class="h-5 w-5 text-red-600"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-red-800">
                        {{ errorMessage }}
                    </p>
                </div>
                <div class="ml-3">
                    <button
                        @click="retryFetch"
                        class="bg-red-100 text-red-800 px-4 py-2 rounded-md text-sm font-medium hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                        Tentar Novamente
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div
            v-else-if="products.length > 0"
            class="bg-white rounded-lg shadow overflow-hidden"
        >
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Nome
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Preço
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Estoque
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Status
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="product in products"
                            :key="product.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ product.name }}
                                </div>
                                <div
                                    v-if="product.description"
                                    class="text-sm text-gray-500 truncate max-w-xs"
                                >
                                    {{ product.description }}
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                R$ {{ formatPrice(product.price) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                {{ product.stock }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        product.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{
                                        product.is_active ? "Ativo" : "Inativo"
                                    }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                            >
                                <router-link
                                    :to="`/products/${product.id}`"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    Ver
                                </router-link>
                                <router-link
                                    :to="`/products/${product.id}/edit`"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                >
                                    Editar
                                </router-link>
                                <button
                                    @click="handleDelete(product)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Excluir
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="pagination"
                class="bg-gray-50 px-6 py-4 border-t border-gray-200"
            >
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Mostrando
                        <span class="font-medium">{{ pagination.from }}</span>
                        a
                        <span class="font-medium">{{ pagination.to }}</span>
                        de
                        <span class="font-medium">{{ pagination.total }}</span>
                        resultados
                    </div>
                    <div class="flex space-x-2">
                        <button
                            @click="goToPage(pagination.current_page - 1)"
                            :disabled="pagination.current_page === 1"
                            class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Anterior
                        </button>
                        <button
                            v-for="page in visiblePages"
                            :key="page"
                            @click="goToPage(page)"
                            :class="[
                                'px-3 py-1 border rounded-md text-sm font-medium',
                                page === pagination.current_page
                                    ? 'bg-blue-600 text-white border-blue-600'
                                    : 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50',
                            ]"
                        >
                            {{ page }}
                        </button>
                        <button
                            @click="goToPage(pagination.current_page + 1)"
                            :disabled="
                                pagination.current_page === pagination.last_page
                            "
                            class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Próxima
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-gray-500 text-lg">Nenhum produto encontrado</p>
            <router-link
                to="/products/new"
                class="mt-4 inline-block text-blue-600 hover:text-blue-700 font-medium"
            >
                Criar primeiro produto
            </router-link>
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
import { ref, computed, watch, onMounted } from "vue";
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

interface Pagination {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
    from: number;
    to: number;
}

const api = useApi();
const { showSuccess } = useNotification();
const { handleError } = useErrorHandler();

const products = ref<Product[]>([]);
const pagination = ref<Pagination | null>(null);
const loading = ref(false);
const errorMessage = ref("");

const searchTerm = ref("");
const statusFilter = ref("");
const currentPage = ref(1);

const showDeleteConfirm = ref(false);
const productToDelete = ref<Product | null>(null);

let searchTimeout: number | null = null;

watch(searchTerm, () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = window.setTimeout(() => {
        currentPage.value = 1;
        fetchProducts();
    }, 300);
});

watch(statusFilter, () => {
    currentPage.value = 1;
    fetchProducts();
});

const visiblePages = computed(() => {
    if (!pagination.value) return [];

    const pages: number[] = [];
    const current = pagination.value.current_page;
    const last = pagination.value.last_page;

    let start = Math.max(1, current - 2);
    let end = Math.min(last, current + 2);

    if (current <= 3) {
        end = Math.min(5, last);
    }
    if (current >= last - 2) {
        start = Math.max(1, last - 4);
    }

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }

    return pages;
});

async function fetchProducts() {
    loading.value = true;
    errorMessage.value = "";

    try {
        const params = new URLSearchParams();
        params.append("page", currentPage.value.toString());
        params.append("per_page", "15");

        if (searchTerm.value) {
            params.append("search", searchTerm.value);
        }

        if (statusFilter.value !== "") {
            params.append("is_active", statusFilter.value);
        }

        const response = await api.get(`/products?${params.toString()}`);

        if (response.success && response.data) {
            products.value = response.data.products || [];
            pagination.value = response.data.pagination || null;
        }
    } catch (error) {
        if (error instanceof ApiError) {
            errorMessage.value = error.message;
            handleError(error, "carregar produtos");
        } else {
            errorMessage.value = "Erro ao carregar produtos";
            handleError(error, "carregar produtos");
        }
    } finally {
        loading.value = false;
    }
}

function retryFetch() {
    errorMessage.value = "";
    fetchProducts();
}

function goToPage(page: number) {
    if (pagination.value && page >= 1 && page <= pagination.value.last_page) {
        currentPage.value = page;
        fetchProducts();
    }
}

function formatPrice(price: string): string {
    const num = parseFloat(price);
    return num.toFixed(2).replace(".", ",");
}

function handleDelete(product: Product) {
    productToDelete.value = product;
    showDeleteConfirm.value = true;
}

function cancelDelete() {
    showDeleteConfirm.value = false;
    productToDelete.value = null;
}

async function confirmDelete() {
    if (!productToDelete.value) return;

    const product = productToDelete.value;
    showDeleteConfirm.value = false;

    try {
        const response = await api.delete(`/products/${product.id}`);

        if (response.success) {
            showSuccess(response.message || "Produto excluído com sucesso");
            fetchProducts();
        }
    } catch (error) {
        handleError(error, "excluir produto");
    } finally {
        productToDelete.value = null;
    }
}

onMounted(() => {
    fetchProducts();
});
</script>
