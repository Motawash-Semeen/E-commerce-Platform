<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ProductForm from '@/Components/ProductForm.vue';

defineProps({
    product: { type: Object, required: true },
    categories: { type: Array, required: true },
});
</script>

<template>
    <Head title="Edit product" />

    <AdminLayout>
        <template #header>
            <PageHeader :back-href="route('admin.products.index')" back-label="Products" :title="`Edit: ${product.name}`" subtitle="Changes here don't touch any shop's own price or description." />
        </template>

        <ProductForm
            :product="product"
            :categories="categories"
            :update-url="route('admin.products.update', product.slug)"
            :cancel-url="route('admin.products.index')"
        />
    </AdminLayout>
</template>
