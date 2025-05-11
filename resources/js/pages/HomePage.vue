<template>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <HomeIntro />
                        <div v-if="store.uploadError" class="alert alert-danger mb-3">
                            {{ store.uploadError }}
                        </div>
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <FileUpload />
                            <LoadAffiliates />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import HomeIntro from '../components/HomeIntro.vue';
import FileUpload from '../components/FileUpload.vue';
import LoadAffiliates from '../components/LoadAffiliates.vue';
import { useAffiliatesStore } from '../stores/affiliates';

const store = useAffiliatesStore();
</script>
