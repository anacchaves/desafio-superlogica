<template>
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Login</h2>

        <form @submit.prevent="handleSubmit">
            <div class="mb-4">
                <label
                    for="email"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Email
                </label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    :class="{
                        'border-red-500':
                            fieldErrors.email || localErrors.email,
                        'border-gray-300':
                            !fieldErrors.email && !localErrors.email,
                    }"
                    @input="onEmailInput"
                    @blur="onEmailInput"
                    required
                />
                <p
                    v-if="localErrors.email || fieldErrors.email"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ localErrors.email || fieldErrors.email }}
                </p>
            </div>

            <div class="mb-6">
                <label
                    for="password"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Senha
                </label>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    :class="{
                        'border-red-500':
                            fieldErrors.password || localErrors.password,
                        'border-gray-300':
                            !fieldErrors.password && !localErrors.password,
                    }"
                    @input="onPasswordInput"
                    required
                />
                <p
                    v-if="localErrors.password || fieldErrors.password"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ localErrors.password || fieldErrors.password }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="loading"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ loading ? "Entrando..." : "Entrar" }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Não tem uma conta?
            <router-link
                to="/register"
                class="text-blue-600 hover:text-blue-700 font-medium"
            >
                Registre-se
            </router-link>
        </p>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "../composables/useAuth";
import { useNotification } from "../composables/useNotification";
import { useErrorHandler } from "../composables/useErrorHandler";
import { ApiError } from "../types/errors";

const router = useRouter();
const { login } = useAuth();
const { showSuccess } = useNotification();
const { handleError, fieldErrors, clearFieldError } = useErrorHandler();

const form = ref({
    email: "",
    password: "",
});

const loading = ref(false);
const localErrors = ref<Record<string, string>>({});

function validateEmail(email: string): string | null {
    if (!email) {
        return "Email é obrigatório";
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        return "Digite um email válido";
    }

    return null;
}

function onEmailInput() {
    clearFieldError("email");

    const error = validateEmail(form.value.email);
    if (error) {
        localErrors.value.email = error;
    } else {
        delete localErrors.value.email;
    }
}

function onPasswordInput() {
    clearFieldError("password");
    delete localErrors.value.password;
}

async function handleSubmit() {
    try {
        loading.value = true;

        await login({
            email: form.value.email,
            password: form.value.password,
        });

        showSuccess("Login realizado com sucesso");
        router.push("/products");
    } catch (error: unknown) {
        if (error instanceof ApiError) {
            if (error.statusCode === 401) {
                handleError(
                    new ApiError("Credenciais inválidas", 401),
                    "login",
                );
            } else {
                handleError(error, "login");
            }
        } else {
            handleError(error, "login");
        }

        form.value.password = "";
    } finally {
        loading.value = false;
    }
}
</script>
