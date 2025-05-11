import { defineStore } from 'pinia';

export const useAffiliatesStore = defineStore('affiliates', {
    state: () => ({
        affiliates: [],
    }),
    actions: {
        setAffiliates(list) {
            this.affiliates = list;
        },
        clearAffiliates() {
            this.affiliates = [];
        }
    }
});
