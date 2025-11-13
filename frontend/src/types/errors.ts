/**
 * Validation errors structure from API responses
 * Maps field names to arrays of error messages
 */
export interface ValidationErrors {
    [field: string]: string[];
}

/**
 * Custom error class for API errors
 * Encapsulates all API error information including status codes,
 * validation errors, and network error flags
 */
export class ApiError extends Error {
    public statusCode: number;
    public validationErrors: ValidationErrors | null;
    public isNetworkError: boolean;

    constructor(
        message: string,
        statusCode: number = 0,
        validationErrors: ValidationErrors | null = null,
        isNetworkError: boolean = false,
    ) {
        super(message);
        this.name = "ApiError";
        this.statusCode = statusCode;
        this.validationErrors = validationErrors;
        this.isNetworkError = isNetworkError;
    }

    /**
     * Check if this is a validation error (422)
     */
    isValidationError(): boolean {
        return this.statusCode === 422 && this.validationErrors !== null;
    }

    /**
     * Check if this is a business rule error (400)
     */
    isBusinessRuleError(): boolean {
        return this.statusCode === 400;
    }

    /**
     * Check if this is an authentication error (401)
     */
    isAuthError(): boolean {
        return this.statusCode === 401;
    }

    /**
     * Check if this is a not found error (404)
     */
    isNotFoundError(): boolean {
        return this.statusCode === 404;
    }

    /**
     * Check if this is a server error (500)
     */
    isServerError(): boolean {
        return this.statusCode === 500;
    }

    /**
     * Get all validation error messages as a flat array
     */
    getValidationMessages(): string[] {
        if (!this.validationErrors) return [];

        return Object.values(this.validationErrors).flat();
    }

    /**
     * Get validation error for a specific field
     */
    getFieldError(field: string): string | null {
        if (!this.validationErrors || !this.validationErrors[field]) {
            return null;
        }

        return this.validationErrors[field][0] || null;
    }

    /**
     * Check if a specific field has validation errors
     */
    hasFieldError(field: string): boolean {
        return !!(this.validationErrors && this.validationErrors[field]);
    }

    /**
     * Get all field names that have validation errors
     */
    getErrorFields(): string[] {
        if (!this.validationErrors) return [];

        return Object.keys(this.validationErrors);
    }
}
