<script setup>
import { ref } from "vue";
import axios from "axios";
import {
    XMarkIcon,
    BoltIcon,
    LockClosedIcon,
    EyeIcon,
    EyeSlashIcon,
} from "@heroicons/vue/24/solid";
import { useAuthModalStore } from "@/stores/authModal";

const authModal = useAuthModalStore();
const email = ref("");
const password = ref("");
const showPassword = ref(false);
const loading = ref(false);
const error = ref("");
const handleSignIn = async () => {
    error.value = "";
    if (!email.value || !password.value) {
        error.value = "Please fill in all fields";
        return;
    }

    loading.value = true;
    try {
        const result = await axios.post("/api/signin", {
            email: email.value,
            password: password.value,
        });

        console.log(result);
        authModal.close();
    } catch (err) {
        error.value = err.response?.data?.message
            ? err.response.data.message
            : "Sign in failed. Please try again.";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="authModal.show"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            >
                <!-- Backdrop -->
                <div
                    class="absolute inset-0 bg-black/80 backdrop-blur-sm"
                    @click="authModal.close()"
                ></div>

                <!-- Modal -->
                <div
                    class="relative bg-[#16161E] rounded-xl border border-white/10 shadow-2xl max-w-md w-full overflow-hidden"
                    :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                >
                    <!-- Header -->
                    <div
                        class="px-6 py-6 border-b border-white/10 flex items-center justify-between"
                    >
                        <div>
                            <h2 class="text-2xl font-bold">Sign In</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                Access your PulseCheck dashboard
                            </p>
                        </div>
                        <button
                            @click="authModal.close()"
                            class="w-8 h-8 rounded-lg hover:bg-white/5 flex items-center justify-center transition-colors group"
                        >
                            <XMarkIcon
                                class="w-5 h-5 text-gray-500 group-hover:text-white"
                            />
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-6 space-y-4">
                        <!-- Error Message -->
                        <div
                            v-if="error"
                            class="p-3 rounded-lg bg-red-500/10 border border-red-500/20"
                        >
                            <p class="text-sm text-red-400">{{ error }}</p>
                        </div>

                        <!-- Email Input -->
                        <div>
                            <label
                                for="email"
                                class="block text-sm font-semibold text-white mb-2"
                            >
                                Email Address
                            </label>
                            <input
                                id="email"
                                v-model="email"
                                type="email"
                                placeholder="you@example.com"
                                class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/20 transition-all"
                                :disabled="loading"
                            />
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label
                                for="password"
                                class="block text-sm font-semibold text-white mb-2"
                            >
                                Password
                            </label>
                            <div class="relative">
                                <input
                                    id="password"
                                    v-model="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="••••••••"
                                    class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/20 transition-all pr-10"
                                    :disabled="loading"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors"
                                    :disabled="loading"
                                >
                                    <EyeIcon
                                        v-if="showPassword"
                                        class="w-5 h-5"
                                    />
                                    <EyeSlashIcon v-else class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Forgot Password Link -->
                        <div class="text-right">
                            <button
                                class="text-xs text-cyan-400 hover:text-cyan-300 transition-colors"
                            >
                                Forgot password?
                            </button>
                        </div>

                        <!-- Sign In Button -->
                        <button
                            @click="handleSignIn"
                            :disabled="loading"
                            class="w-full px-4 py-2.5 mt-6 bg-gradient-to-r from-cyan-600/80 to-teal-600/80 hover:from-cyan-600 hover:to-teal-600 disabled:from-gray-600 disabled:to-gray-600 rounded-lg text-sm font-semibold transition-all disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <BoltIcon v-if="!loading" class="w-4 h-4" />
                            <div
                                v-else
                                class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"
                            ></div>
                            <span>{{
                                loading ? "Signing in..." : "Sign In"
                            }}</span>
                        </button>

                        <!-- Sign Up Link -->
                        <div class="text-center pt-2">
                            <p class="text-sm text-gray-500">
                                Don't have an account?
                                <button
                                    class="text-cyan-400 hover:text-cyan-300 font-semibold transition-colors"
                                >
                                    Sign up
                                </button>
                            </p>
                        </div>
                    </div>

                    <!-- Footer Info -->
                    <div class="px-6 py-4 bg-[#1A1A24] border-t border-white/5">
                        <div class="flex items-start gap-3">
                            <LockClosedIcon
                                class="w-4 h-4 text-cyan-400 flex-shrink-0 mt-0.5"
                            />
                            <p class="text-xs text-gray-500">
                                Your login is secure. We use industry-standard
                                encryption.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
