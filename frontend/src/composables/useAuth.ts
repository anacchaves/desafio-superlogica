import { ref, computed } from "vue";
import { useApi } from "./useApi";

interface User {
    id: number;
    name: string;
    email: string;
    created_at: string;
}

interface LoginCredentials {
    email: string;
    password: string;
}

interface RegisterData {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
}

const user = ref<User | null>(null);
const token = ref<string | null>(null);

const storedToken = localStorage.getItem("token");
const storedUser = localStorage.getItem("user");

if (storedToken) {
    token.value = storedToken;
}

if (storedUser) {
    try {
        user.value = JSON.parse(storedUser);
    } catch (e) {
        localStorage.removeItem("user");
    }
}

export function useAuth() {
    const api = useApi();

    const isAuthenticated = computed(() => !!token.value);

    async function login(credentials: LoginCredentials) {
        const response = await api.post("/login", credentials);

        if (response.success && response.data) {
            token.value = response.data.token;
            user.value = response.data.user;

            localStorage.setItem("token", response.data.token);
            localStorage.setItem("user", JSON.stringify(response.data.user));

            const redirectUrl = sessionStorage.getItem("redirectAfterLogin");

            if (redirectUrl) {
                sessionStorage.removeItem("redirectAfterLogin");
                window.location.href = redirectUrl;
            } else {
                window.location.href = "/products";
            }
        }

        return response;
    }

    async function register(data: RegisterData) {
        const response = await api.post("/register", data);

        if (response.success && response.data) {
            token.value = response.data.token;
            user.value = response.data.user;

            localStorage.setItem("token", response.data.token);
            localStorage.setItem("user", JSON.stringify(response.data.user));
        }

        return response;
    }

    async function logout() {
        try {
            await api.post("/logout", {});
        } finally {
            token.value = null;
            user.value = null;

            localStorage.removeItem("token");
            localStorage.removeItem("user");
        }
    }

    return {
        user,
        token,
        isAuthenticated,
        login,
        register,
        logout,
    };
}
