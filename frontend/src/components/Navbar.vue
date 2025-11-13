<template>
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand -->
                <div class="flex items-center">
                    <router-link
                        to="/products"
                        class="text-xl font-bold text-gray-900"
                    >
                        Sistema de Produtos
                    </router-link>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center space-x-4">
                    <router-link
                        to="/products"
                        class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                    >
                        Produtos
                    </router-link>

                    <!-- User Info -->
                    <div
                        v-if="user"
                        class="flex items-center space-x-4 border-l border-gray-200 pl-4"
                    >
                        <span class="text-sm text-gray-700">
                            {{ user.name }}
                        </span>
                        <button
                            @click="handleLogout"
                            class="text-sm text-red-600 hover:text-red-700 font-medium"
                        >
                            Sair
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup lang="ts">
import { useRouter } from "vue-router";
import { useAuth } from "../composables/useAuth";
import { useNotification } from "../composables/useNotification";

const router = useRouter();
const { user, logout } = useAuth();
const { showSuccess } = useNotification();

async function handleLogout() {
    await logout();
    showSuccess("Logout realizado com sucesso");
    router.push("/login");
}
</script>
