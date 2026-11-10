<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ProductForm from '@/Components/ProductForm.vue';

defineProps({
    categories: { type: Array, required: true },
});
</script>

<template>
    <Head title="New product" />

    <AdminLayout>
        <template #header>
            <PageHeader title="New catalog product" subtitle="Shop owners set their own price when they list it." />
        </template>

        <ProductForm
            :categories="categories"
            :store-url="route('admin.products.store')"
            :cancel-url="route('admin.products.index')"
        />
    </AdminLayout>
</template>
