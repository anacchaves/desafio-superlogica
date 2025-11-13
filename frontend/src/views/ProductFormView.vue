<template>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ isEditMode ? "Editar Produto" : "Criar Produto" }}
                </h1>
            </div>

            <!-- Loading State -->
            <LoadingSpinner v-if="loadingProduct" size="lg" />

            <!-- Form -->
            <div v-else class="bg-white rounded-lg shadow p-6">
                <form @submit.prevent="handleSubmit">
                    <!-- Name -->
                    <div class="mb-4">
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Nome <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="
                                hasFieldError('name') || localErrors.name
                                    ? 'border-red-500'
                                    : 'border-gray-300'
                            "
                            @blur="validateField('name')"
                            required
                        />
                        <p
                            v-if="localErrors.name || hasFieldError('name')"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ localErrors.name || getFieldError("name") }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label
                            for="description"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Descrição
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="
                                hasFieldError('description')
                                    ? 'border-red-500'
                                    : 'border-gray-300'
                            "
                        ></textarea>
                        <p
                            v-if="hasFieldError('description')"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ getFieldError("description") }}
                        </p>
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                        <label
                            for="price"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Preço (R$) <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="price"
                            v-model="form.price"
                            type="number"
                            step="0.01"
                            min="0.01"
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="
                                hasFieldError('price') || localErrors.price
                                    ? 'border-red-500'
                                    : 'border-gray-300'
                            "
                            @blur="validateField('price')"
                            required
                        />
                        <p
                            v-if="localErrors.price || hasFieldError('price')"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ localErrors.price || getFieldError("price") }}
                        </p>
                        <p
                            v-if="
                                isEditMode &&
                                priceRange &&
                                !localErrors.price &&
                                !hasFieldError('price')
                            "
                            class="mt-1 text-sm text-gray-600"
                        >
                            Faixa permitida: R$
                            {{ formatPrice(priceRange.min) }} - R$
                            {{ formatPrice(priceRange.max) }}
                        </p>
                    </div>

                    <!-- Stock -->
                    <div class="mb-6">
                        <label
                            for="stock"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Estoque <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="stock"
                            v-model="form.stock"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="
                                hasFieldError('stock') || localErrors.stock
                                    ? 'border-red-500'
                                    : 'border-gray-300'
                            "
                            @blur="validateField('stock')"
                            required
                        />
                        <p
                            v-if="localErrors.stock || hasFieldError('stock')"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ localErrors.stock || getFieldError("stock") }}
                        </p>
                        <p
                            v-if="!localErrors.stock && !hasFieldError('stock')"
                            class="mt-1 text-sm text-gray-600"
                        >
                            Produtos com estoque zero serão marcados como
                            inativos automaticamente
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="handleCancel"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{
                                loading
                                    ? "Salvando..."
                                    : isEditMode
                                      ? "Atualizar"
                                      : "Criar"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useApi } from "../composables/useApi";
import { useNotification } from "../composables/useNotification";
import { useErrorHandler } from "../composables/useErrorHandler";
import LoadingSpinner from "../components/LoadingSpinner.vue";

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

interface PriceRange {
    min: string;
    max: string;
}

const route = useRoute();
const router = useRouter();
const api = useApi();
const { showSuccess } = useNotification();
const { handleError, clearFieldError, getFieldError, hasFieldError } =
    useErrorHandler();

const form = ref({
    name: "",
    description: "",
    price: "",
    stock: 0,
});

const loading = ref(false);
const loadingProduct = ref(false);
const originalPrice = ref<number | null>(null);
const localErrors = ref<Record<string, string>>({});

const isEditMode = computed(() => !!route.params.id);

const priceRange = computed<PriceRange | null>(() => {
    if (!isEditMode.value || originalPrice.value === null) {
        return null;
    }

    const min = originalPrice.value * 0.7;
    const max = originalPrice.value * 1.3;

    return {
        min: min.toFixed(2),
        max: max.toFixed(2),
    };
});

function validateName(name: string): string | null {
    if (!name || name.trim() === "") {
        return "Nome é obrigatório";
    }
    if (name.length > 255) {
        return "Nome não pode ter mais de 255 caracteres";
    }
    return null;
}

function validatePrice(price: string | number): string | null {
    const priceNum = typeof price === "string" ? parseFloat(price) : price;

    if (!price || isNaN(priceNum)) {
        return "Preço é obrigatório";
    }
    if (priceNum <= 0) {
        return "Preço deve ser maior que zero";
    }
    return null;
}

function validateStock(stock: string | number): string | null {
    const stockNum = typeof stock === "string" ? parseInt(stock) : stock;

    if (stock === "" || stock === null || stock === undefined) {
        return "Estoque é obrigatório";
    }
    if (isNaN(stockNum)) {
        return "Estoque deve ser um número";
    }
    if (stockNum < 0) {
        return "Estoque não pode ser negativo";
    }
    return null;
}

function validateField(field: string) {
    let error: string | null = null;

    switch (field) {
        case "name":
            error = validateName(form.value.name);
            break;
        case "price":
            error = validatePrice(form.value.price);
            break;
        case "stock":
            error = validateStock(form.value.stock);
            break;
    }

    if (error) {
        localErrors.value[field] = error;
    } else {
        delete localErrors.value[field];
    }
}

watch(
    () => form.value.name,
    () => {
        if (hasFieldError("name")) {
            clearFieldError("name");
        }
        validateField("name");
    },
);

watch(
    () => form.value.description,
    () => {
        if (hasFieldError("description")) {
            clearFieldError("description");
        }
    },
);

watch(
    () => form.value.price,
    () => {
        if (hasFieldError("price")) {
            clearFieldError("price");
        }
        validateField("price");
    },
);

watch(
    () => form.value.stock,
    () => {
        if (hasFieldError("stock")) {
            clearFieldError("stock");
        }
        validateField("stock");
    },
);

async function fetchProduct() {
    if (!isEditMode.value) return;

    try {
        loadingProduct.value = true;
        const productId = route.params.id;
        const response = await api.get(`/products/${productId}`);

        if (response.success && response.data) {
            const product: Product = response.data;
            form.value = {
                name: product.name,
                description: product.description || "",
                price: product.price,
                stock: product.stock,
            };
            originalPrice.value = parseFloat(product.price);
        }
    } catch (error) {
        handleError(error, "carregar produto");
        router.push("/products");
    } finally {
        loadingProduct.value = false;
    }
}

async function handleSubmit() {
    localErrors.value = {};

    const nameError = validateName(form.value.name);
    if (nameError) localErrors.value.name = nameError;

    const priceError = validatePrice(form.value.price);
    if (priceError) localErrors.value.price = priceError;

    const stockError = validateStock(form.value.stock);
    if (stockError) localErrors.value.stock = stockError;

    if (Object.keys(localErrors.value).length > 0) {
        return;
    }

    try {
        loading.value = true;

        const payload = {
            name: form.value.name,
            description: form.value.description || null,
            price: parseFloat(form.value.price),
            stock: parseInt(form.value.stock.toString()),
        };

        let response;

        if (isEditMode.value) {
            const productId = route.params.id;
            response = await api.put(`/products/${productId}`, payload);
        } else {
            response = await api.post("/products", payload);
        }

        if (response.success) {
            showSuccess(
                response.message ||
                    (isEditMode.value
                        ? "Produto atualizado com sucesso"
                        : "Produto criado com sucesso"),
            );
            router.push("/products");
        }
    } catch (error) {
        handleError(
            error,
            isEditMode.value ? "atualizar produto" : "criar produto",
        );
    } finally {
        loading.value = false;
    }
}

function handleCancel() {
    router.push("/products");
}

function formatPrice(price: string): string {
    const num = parseFloat(price);
    return num.toFixed(2).replace(".", ",");
}

onMounted(() => {
    fetchProduct();
});
</script>
