import { ApiError } from "../types/errors";
import type { ValidationErrors } from "../types/errors";

export function useApi() {
    const baseURL = import.meta.env.VITE_API_URL;

    function extractErrorInfo(
        response: Response,
        data: any,
    ): {
        message: string;
        validationErrors: ValidationErrors | null;
    } {
        let message = "Erro na requisição";
        let validationErrors: ValidationErrors | null = null;

        if (data.message) {
            message = data.message;
        } else if (data.error) {
            message = data.error;
        }

        if (response.status === 422 && data.errors) {
            validationErrors = data.errors;

            if (!data.message) {
                const errorCount = Object.keys(data.errors).length;
                message = `Erro de validação em ${errorCount} campo(s)`;
            }
        }

        return { message, validationErrors };
    }

    async function request(endpoint: string, options: RequestInit = {}) {
        const token = localStorage.getItem("token");

        const config: RequestInit = {
            ...options,
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                ...(token && { Authorization: `Bearer ${token}` }),
                ...options.headers,
            },
        };

        try {
            const response = await fetch(`${baseURL}${endpoint}`, config);

            if (response.status === 401) {
                localStorage.removeItem("token");
                localStorage.removeItem("user");

                const currentPath = window.location.pathname;
                if (currentPath !== "/login" && currentPath !== "/register") {
                    sessionStorage.setItem("redirectAfterLogin", currentPath);
                }

                window.location.href = "/login";
                throw new ApiError(
                    "Sessão expirada. Por favor, faça login novamente",
                    401,
                );
            }

            let data: any;
            try {
                data = await response.json();
            } catch (e) {
                if (!response.ok) {
                    throw new ApiError(
                        `Erro HTTP ${response.status}`,
                        response.status,
                    );
                }
                data = {};
            }

            if (!response.ok) {
                const { message, validationErrors } = extractErrorInfo(
                    response,
                    data,
                );

                throw new ApiError(message, response.status, validationErrors);
            }

            return data;
        } catch (error) {
            if (error instanceof ApiError) {
                throw error;
            }

            if (error instanceof TypeError && error.message.includes("fetch")) {
                throw new ApiError(
                    "Erro de conexão. Verifique sua internet e tente novamente",
                    0,
                    null,
                    true,
                );
            }

            throw new ApiError("Erro inesperado. Tente novamente", 0);
        }
    }

    return {
        get: (endpoint: string) => request(endpoint, { method: "GET" }),
        post: (endpoint: string, body: any) =>
            request(endpoint, { method: "POST", body: JSON.stringify(body) }),
        put: (endpoint: string, body: any) =>
            request(endpoint, { method: "PUT", body: JSON.stringify(body) }),
        delete: (endpoint: string) => request(endpoint, { method: "DELETE" }),
    };
}
