<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';

defineProps({
    stats: { type: Object, required: true },   // { shops, owners, products, listings }
});

const user = usePage().props.auth.user;

const cards = [
    { key: 'shops', label: 'Shops', hint: 'Manage shops', route: 'admin.shops.index', tone: 'from-indigo-500 to-purple-500' },
    { key: 'owners', label: 'Shop owners', hint: 'Manage accounts', route: 'admin.shop-owners.index', tone: 'from-sky-500 to-indigo-500' },
    { key: 'products', label: 'Catalog products', hint: 'Manage catalog', route: 'admin.products.index', tone: 'from-emerald-500 to-teal-500' },
    { key: 'listings', label: 'Shop listings', hint: 'Products for sale in shops', route: null, tone: 'from-amber-500 to-orange-500' },
];

const quickLinks = [
    { label: 'Add product', route: 'admin.products.create', primary: true },
    { label: 'Add shop', route: 'admin.shops.create' },
    { label: 'Add shop owner', route: 'admin.shop-owners.create' },
    { label: 'Categories', route: 'admin.categories.index' },
    { label: 'Subcategories', route: 'admin.subcategories.index' },
];
</script>

<template>
    <Head title="Admin dashboard" />

    <AdminLayout>
        <template #header>
            <PageHeader title="Admin dashboard" :subtitle="`Welcome back, ${user.name}.`">
                <Link :href="route('home')" class="btn-secondary">View storefront</Link>
            </PageHeader>
        </template>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <component
                :is="card.route ? Link : 'div'"
                v-for="card in cards"
                :key="card.key"
                :href="card.route ? route(card.route) : undefined"
                class="card group relative overflow-hidden p-6 transition"
                :class="{ 'hover:-translate-y-0.5 hover:shadow-md': card.route }"
            >
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r" :class="card.tone" />
                <p class="text-sm font-medium text-gray-500">{{ card.label }}</p>
                <p class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">{{ stats[card.key] }}</p>
                <p class="mt-1 text-xs text-gray-400" :class="{ 'group-hover:text-indigo-600': card.route }">{{ card.hint }}</p>
            </component>
        </div>

        <div class="card mt-6 p-6">
            <h2 class="text-base font-semibold text-gray-900">Quick links</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                <Link
                    v-for="link in quickLinks"
                    :key="link.route"
                    :href="route(link.route)"
                    :class="link.primary ? 'btn-primary' : 'btn-secondary'"
                >{{ link.label }}</Link>
                <a :href="route('log-viewer.index')" class="btn-secondary">View logs</a>
            </div>
        </div>
    </AdminLayout>
</template>
