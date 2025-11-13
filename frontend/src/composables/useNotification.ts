import { ref } from "vue";

export interface Notification {
    id: number;
    type: "success" | "error" | "info";
    message: string;
}

const notifications = ref<Notification[]>([]);
let notificationId = 0;

export function useNotification() {
    function showSuccess(message: string, timeout = 5000) {
        const id = ++notificationId;
        const notification: Notification = {
            id,
            type: "success",
            message,
        };

        notifications.value.push(notification);

        if (timeout > 0) {
            setTimeout(() => {
                dismiss(id);
            }, timeout);
        }
    }

    function showError(message: string, timeout = 5000) {
        const id = ++notificationId;
        const notification: Notification = {
            id,
            type: "error",
            message,
        };

        notifications.value.push(notification);

        if (timeout > 0) {
            setTimeout(() => {
                dismiss(id);
            }, timeout);
        }
    }

    function showInfo(message: string, timeout = 5000) {
        const id = ++notificationId;
        const notification: Notification = {
            id,
            type: "info",
            message,
        };

        notifications.value.push(notification);

        if (timeout > 0) {
            setTimeout(() => {
                dismiss(id);
            }, timeout);
        }
    }

    function dismiss(id: number) {
        const index = notifications.value.findIndex((n) => n.id === id);
        if (index !== -1) {
            notifications.value.splice(index, 1);
        }
    }

    return {
        notifications,
        showSuccess,
        showError,
        showInfo,
        dismiss,
    };
}
