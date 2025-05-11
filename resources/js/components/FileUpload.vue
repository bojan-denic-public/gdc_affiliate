<template>
    <div>
        <label for="file-upload" class="btn btn-primary">Upload Affiliates File</label>
        <input id="file-upload" type="file" @change="onFileChange" name="affiliates" style="display:none" />
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAffiliatesStore } from '../stores/affiliates';
const router = useRouter();
const store = useAffiliatesStore();
async function onFileChange(event) {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('affiliates', file);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
        const response = await fetch('/api/invite-affiliates', {
            method: 'POST',
            body: formData,
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
