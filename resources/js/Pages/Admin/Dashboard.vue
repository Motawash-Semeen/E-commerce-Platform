<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    stats: { type: Object, required: true },   // { shops, owners, products }
});

const user = usePage().props.auth.user;

// Pages not yet converted to Vue are opened with a normal link (full page load).
const cards = [
    { key: 'shops', label: 'Shops', href: null },
    { key: 'owners', label: 'Shop owners', href: route('admin.shop-owners.index') },
    { key: 'products', label: 'Catalog products', href: route('admin.products.index') },
];
</script>

<template>
    <Head title="Admin dashboard" />

    <AdminLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900">Admin dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Welcome back, {{ user.name }}.</p>
        </template>

        <div class="grid gap-4 sm:grid-cols-3">
            <component
                :is="card.href ? 'a' : 'div'"
                v-for="card in cards"
                :key="card.key"
                :href="card.href"
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition"
                :class="{ 'hover:-translate-y-0.5 hover:shadow-md': card.href }"
            >
                <p class="text-sm font-medium text-gray-500">{{ card.label }}</p>
                <p class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">{{ stats[card.key] }}</p>
            </component>
        </div>

        <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Quick links</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                <a :href="route('admin.products.create')" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Add product</a>
                <a :href="route('admin.shop-owners.create')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Add shop owner</a>
                <a :href="route('admin.categories.index')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Categories</a>
                <a :href="route('log-viewer.index')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">View logs</a>
            </div>
        </div>
    </AdminLayout>
</template>
