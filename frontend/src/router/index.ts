import { createRouter, createWebHistory } from "vue-router";
import type { RouteRecordRaw } from "vue-router";
import { useAuth } from "../composables/useAuth";

// Layouts
import AuthLayout from "../layouts/AuthLayout.vue";
import DashboardLayout from "../layouts/DashboardLayout.vue";

// Views
import LoginView from "../views/LoginView.vue";
import RegisterView from "../views/RegisterView.vue";
import ProductListView from "../views/ProductListView.vue";
import ProductDetailView from "../views/ProductDetailView.vue";
import ProductFormView from "../views/ProductFormView.vue";

const routes: RouteRecordRaw[] = [
    // Public routes (authentication pages)
    {
        path: "/",
        component: AuthLayout,
        meta: { requiresGuest: true },
        children: [
            {
                path: "",
                redirect: "/login",
            },
            {
                path: "login",
                name: "login",
                component: LoginView,
            },
            {
                path: "register",
                name: "register",
                component: RegisterView,
            },
        ],
    },

    // Protected routes (product management)
    {
        path: "/products",
        component: DashboardLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: "",
                name: "products",
                component: ProductListView,
            },
            {
                path: "new",
                name: "product-create",
                component: ProductFormView,
            },
            {
                path: ":id",
                name: "product-detail",
                component: ProductDetailView,
            },
            {
                path: ":id/edit",
                name: "product-edit",
                component: ProductFormView,
            },
        ],
    },

    // Catch-all redirect
    {
        path: "/:pathMatch(.*)*",
        redirect: "/login",
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guard
router.beforeEach((to, _from, next) => {
    const { isAuthenticated } = useAuth();

    // Check if route requires authentication
    if (to.meta.requiresAuth && !isAuthenticated.value) {
        // Redirect to login if not authenticated
        next({ name: "login" });
    }
    // Check if route requires guest (login/register pages)
    else if (to.meta.requiresGuest && isAuthenticated.value) {
        // Redirect to products if already authenticated
        next({ name: "products" });
    }
    // Allow navigation
    else {
        next();
    }
});

export default router;
