import { defineStore } from 'pinia';

export const useAffiliatesStore = defineStore('affiliates', {
    state: () => ({
        affiliates: [],
        uploadError: null,
    }),
    actions: {
        setAffiliates(list) {
            this.affiliates = list;
        },
        clearUploadError() {
            this.uploadError = null;
        }
    }
});
