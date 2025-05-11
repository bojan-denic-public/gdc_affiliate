<template>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <nav aria-label="breadcrumb" class="mb-3">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <router-link to="/home">Home</router-link>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Affiliates</li>
                            </ol>
                        </nav>
                        <h2 class="mb-4">List of affiliates matching the criteria</h2>
                        <div v-if="loading" class="text-center my-4">
                            <div class="spinner-border" role="status"></div>
                        </div>
                        <div v-if="error" class="alert alert-warning">{{ error }}</div>
                        <table v-else-if="affiliates.length"
                            class="table table-bordered table-striped table-sm align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="a in affiliates" :key="a.affiliate_id">
                                    <td class="text-center">{{ a.affiliate_id }}</td>
                                    <td class="text-center"><small>{{ a.name }}</small></td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else-if="!loading" class="text-muted mt-3">
                            No affiliates found.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useAffiliatesStore } from '../stores/affiliates';

const affiliates = ref([]);
const error = ref('');
const loading = ref(true);
const route = useRoute();
const store = useAffiliatesStore();

onMounted(() => {
    if (store.affiliates.length) {
        affiliates.value = store.affiliates;
        loading.value = false;
    } else {
        error.value = 'No affiliates to display. Please load or upload a file first from the home page.';
        loading.value = false;
    }
});
</script>
