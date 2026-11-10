<script setup>
import { Head } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ListingForm from './Partials/ListingForm.vue';

defineProps({
    listing: { type: Object, required: true },
    product: { type: Object, required: true },
});
</script>

<template>
    <Head title="Edit listing" />

    <OwnerLayout>
        <template #header>
            <PageHeader :back-href="route('owner.listings.index')" back-label="My listings" :title="`Edit listing: ${product.name}`" subtitle="Only your shop's listing changes. The catalog product stays the same." />
        </template>

        <ListingForm :product="product" :listing="listing" />
    </OwnerLayout>
</template>
