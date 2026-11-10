<script setup>
import { Head } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ListingForm from '@/Pages/Owner/Listings/Partials/ListingForm.vue';

defineProps({
    product: { type: Object, required: true },
});
</script>

<template>
    <Head title="Add to my shop" />

    <OwnerLayout>
        <template #header>
            <PageHeader :title="`Add to my shop: ${product.name}`" subtitle="Set your own price and details. Other shops aren't affected." />
        </template>

        <ListingForm :product="product" />
    </OwnerLayout>
</template>
