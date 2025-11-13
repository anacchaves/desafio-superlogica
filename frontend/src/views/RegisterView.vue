<template>
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">
            Registrar
        </h2>

        <form @submit.prevent="handleSubmit">
            <div class="mb-4">
                <label
                    for="name"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Nome
                </label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    :class="{
                        'border-red-500': errors.name,
                        'border-gray-300': !errors.name,
                    }"
                    @input="onFieldInput('name')"
                    @blur="onFieldInput('name')"
                    required
                />
                <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                    {{ errors.name }}
                </p>
            </div>

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
                        'border-red-500': errors.email,
                        'border-gray-300': !errors.email,
                    }"
                    @input="onFieldInput('email')"
                    @blur="onFieldInput('email')"
                    required
                />
                <p v-if="errors.email" class="mt-1 text-sm text-red-600">
                    {{ errors.email }}
                </p>
            </div>

            <div class="mb-4">
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
                        'border-red-500': errors.password,
                        'border-gray-300': !errors.password,
                    }"
                    @input="onFieldInput('password')"
                    @blur="onFieldInput('password')"
                    required
                />
                <p v-if="errors.password" class="mt-1 text-sm text-red-600">
                    {{ errors.password }}
                </p>
            </div>

            <div class="mb-6">
                <label
                    for="password_confirmation"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Confirmar Senha
                </label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    :class="{
                        'border-red-500': errors.password_confirmation,
                        'border-gray-300': !errors.password_confirmation,
                    }"
                    @input="onFieldInput('password_confirmation')"
                    @blur="onFieldInput('password_confirmation')"
                    required
                />
                <p
                    v-if="errors.password_confirmation"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ errors.password_confirmation }}
                </p>
            </div>

            <p
                v-if="errorMessage"
                class="mb-4 text-sm text-red-600 text-center"
            >
                {{ errorMessage }}
            </p>

            <button
                type="submit"
                :disabled="loading"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ loading ? "Registrando..." : "Registrar" }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Já tem uma conta?
            <router-link
                to="/login"
                class="text-blue-600 hover:text-blue-700 font-medium"
            >
                Faça login
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
const { register } = useAuth();
const { showSuccess } = useNotification();
const { handleError, clearFieldError } = useErrorHandler();

const form = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const errors = ref<Record<string, string>>({});
const errorMessage = ref("");
const loading = ref(false);

function validateName(name: string): string | null {
    if (!name) {
        return "Nome é obrigatório";
    }
    if (name.length > 255) {
        return "Nome não pode ter mais de 255 caracteres";
    }
    return null;
}

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

function validatePassword(password: string): string | null {
    if (!password) {
        return "Senha é obrigatória";
    }
    if (password.length < 8) {
        return "Senha deve ter no mínimo 8 caracteres";
    }
    return null;
}

function validatePasswordConfirmation(
    password: string,
    confirmation: string,
): string | null {
    if (!confirmation) {
        return "Confirmação de senha é obrigatória";
    }
    if (password !== confirmation) {
        return "As senhas não coincidem";
    }
    return null;
}

function validateForm(): boolean {
    errors.value = {};

    const nameError = validateName(form.value.name);
    if (nameError) errors.value.name = nameError;

    const emailError = validateEmail(form.value.email);
    if (emailError) errors.value.email = emailError;

    const passwordError = validatePassword(form.value.password);
    if (passwordError) errors.value.password = passwordError;

    const confirmError = validatePasswordConfirmation(
        form.value.password,
        form.value.password_confirmation,
    );
    if (confirmError) errors.value.password_confirmation = confirmError;

    return Object.keys(errors.value).length === 0;
}

async function handleSubmit() {
    errorMessage.value = "";
    errors.value = {};

    if (!validateForm()) {
        return;
    }

    try {
        loading.value = true;

        await register({
            name: form.value.name,
            email: form.value.email,
            password: form.value.password,
            password_confirmation: form.value.password_confirmation,
        });

        showSuccess("Registro realizado com sucesso");
        router.push("/products");
    } catch (error: any) {
        if (error instanceof ApiError) {
            if (
                error.validationErrors?.email &&
                error.validationErrors.email.length > 0
            ) {
                const emailError = error.validationErrors.email[0];
                if (emailError) {
                    if (
                        emailError.toLowerCase().includes("já") ||
                        emailError.toLowerCase().includes("uso")
                    ) {
                        errors.value.email = "Este email já está em uso";
                    } else {
                        errors.value.email = emailError;
                    }
                }
            }

            if (error.validationErrors) {
                Object.entries(error.validationErrors).forEach(
                    ([field, messages]) => {
                        if (
                            field !== "email" &&
                            messages.length > 0 &&
                            messages[0]
                        ) {
                            errors.value[field] = messages[0];
                        }
                    },
                );
            }

            handleError(error, "registro");

            form.value.password = "";
            form.value.password_confirmation = "";
        } else {
            errorMessage.value = "Erro ao registrar. Tente novamente.";
            handleError(error, "registro");

            form.value.password = "";
            form.value.password_confirmation = "";
        }
    } finally {
        loading.value = false;
    }
}

function onFieldInput(field: string) {
    clearFieldError(field);

    let error: string | null = null;

    switch (field) {
        case "name":
            error = validateName(form.value.name);
            break;
        case "email":
            error = validateEmail(form.value.email);
            break;
        case "password":
            error = validatePassword(form.value.password);
            if (form.value.password_confirmation) {
                const confirmError = validatePasswordConfirmation(
                    form.value.password,
                    form.value.password_confirmation,
                );
                if (confirmError) {
                    errors.value.password_confirmation = confirmError;
                } else {
                    delete errors.value.password_confirmation;
                }
            }
            break;
        case "password_confirmation":
            error = validatePasswordConfirmation(
                form.value.password,
                form.value.password_confirmation,
            );
            break;
    }

    if (error) {
        errors.value[field] = error;
    } else {
        delete errors.value[field];
    }
}
</script>
