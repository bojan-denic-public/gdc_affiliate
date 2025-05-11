<template>
    <button class="btn btn-success" @click="goToAffiliates">
        Load Affiliates from Source File
    </button>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAffiliatesStore } from '../stores/affiliates';
const router = useRouter();
const store = useAffiliatesStore();

async function goToAffiliates() {
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
        const response = await fetch('/api/invite-affiliates', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });
        const data = await response.json();
        if (data.affiliates) {
            store.setAffiliates(data.affiliates);
            router.push({ name: 'AffiliatesList' });
        } else if (data.error) {
            alert(data.error);
        }
    } catch (e) {
        alert('Failed to upload or process file.');
    }
}
</script>
