import { defineStore } from "pinia";
import { ref, computed } from "vue";
import api from "@/utils/axios";

export const useAuthStore = defineStore("auth", () => {
    const user = ref((() => {
        try {
            return JSON.parse(localStorage.getItem("user") || "null");
        } catch {
            return null;
        }
    })());
    const token = ref(localStorage.getItem("auth_token"));
    const isAuthenticated = computed(() => !!token.value);

    const fetchUser = async () => {
        try {
            const { data } = await api.get("/api/user");
            user.value = data;
            localStorage.setItem("user", JSON.stringify(data));
        } catch {
            clear();
        }
    };

    const setSession = (newToken, userData) => {
        token.value = newToken;
        user.value = userData;
        localStorage.setItem("auth_token", newToken);
        localStorage.setItem("user", JSON.stringify(userData));
    };

    const clear = () => {
        token.value = null;
        user.value = null;
        localStorage.removeItem("auth_token");
        localStorage.removeItem("user");
    };

    const logout = async () => {
        try {
            await api.post("/api/signout");
        } catch {
            // ignore errors, always clear session
        }
        clear();
        window.location.href = "/";
    };

    return { user, token, isAuthenticated, fetchUser, setSession, logout };
});
