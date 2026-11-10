<script setup>
import { Head } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ProductForm from '@/Components/ProductForm.vue';

defineProps({
    product: { type: Object, required: true },
    categories: { type: Array, required: true },
});
</script>

<template>
    <Head title="Edit product" />

    <OwnerLayout>
        <template #header>
            <PageHeader :title="`Edit: ${product.name}`" subtitle="This changes the catalog entry. Each shop keeps its own price and description." />
        </template>

        <ProductForm
            :product="product"
            :categories="categories"
            :update-url="route('owner.products.update', product.slug)"
            :cancel-url="route('owner.products.index')"
        />
    </OwnerLayout>
</template>
