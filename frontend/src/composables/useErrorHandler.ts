import { ref } from "vue";
import { ApiError } from "../types/errors";
import { useNotification } from "./useNotification";

export function useErrorHandler() {
    const { showError } = useNotification();
    const fieldErrors = ref<Record<string, string>>({});
    const generalError = ref<string>("");

    function handleError(error: unknown, context: string = "operação") {
        fieldErrors.value = {};
        generalError.value = "";

        if (error instanceof ApiError) {
            generalError.value = error.message;
            showError(error.message);

            if (error.isValidationError() && error.validationErrors) {
                for (const [field, messages] of Object.entries(
                    error.validationErrors,
                )) {
                    if (messages.length > 0 && messages[0]) {
                        fieldErrors.value[field] = messages[0];
                    }
                }
            }
        } else {
            const message = `Erro ao processar ${context}. Tente novamente`;
            generalError.value = message;
            showError(message);
        }
    }

    function clearErrors() {
        fieldErrors.value = {};
        generalError.value = "";
    }

    function clearFieldError(field: string) {
        delete fieldErrors.value[field];
    }

    function hasFieldError(field: string): boolean {
        return !!fieldErrors.value[field];
    }

    function getFieldError(field: string): string {
        return fieldErrors.value[field] || "";
    }

    return {
        fieldErrors,
        generalError,
        handleError,
        clearErrors,
        clearFieldError,
        hasFieldError,
        getFieldError,
    };
}
