import { defineStore } from "pinia";

export const useAuthModalStore = defineStore("authModal", {
    state: () => ({
        show: false,
        mode: "signin",
    }),

    actions: {
        open(type = "signin") {
            this.mode = type;
            this.show = true;
        },

        close() {
            this.show = false;
        },
    },
});
