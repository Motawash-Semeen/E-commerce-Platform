<script setup>
import { Head } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ProductForm from '@/Components/ProductForm.vue';

defineProps({
    categories: { type: Array, required: true },
    hasShop: { type: Boolean, required: true },
});
</script>

<template>
    <Head title="Create product" />

    <OwnerLayout>
        <template #header>
            <PageHeader :back-href="route('owner.products.index')" back-label="My products" title="Create a catalog product" subtitle="It joins the shared catalog, so other shops can list it too." />
        </template>

        <ProductForm
            :categories="categories"
            :store-url="route('owner.products.store')"
            :cancel-url="route('owner.products.index')"
            :can-add-to-shop="hasShop"
        />
    </OwnerLayout>
</template>
