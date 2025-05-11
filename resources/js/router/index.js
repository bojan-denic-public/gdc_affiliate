import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../pages/HomePage.vue';
import AffiliatesListPage from '../pages/AffiliatesListPage.vue';

const routes = [
    { path: '/', redirect: '/home' },
    { path: '/home', name: 'Home', component: HomePage },
    { path: '/affiliates', name: 'AffiliatesList', component: AffiliatesListPage },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
