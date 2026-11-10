<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ShopForm from './Partials/ShopForm.vue';

defineProps({
    shop: { type: Object, required: true },
    owners: { type: Array, required: true },
});
</script>

<template>
    <Head title="Edit shop" />

    <AdminLayout>
        <template #header>
            <PageHeader :title="`Edit: ${shop.name}`" />
        </template>

        <ShopForm :shop="shop" :owners="owners" />
    </AdminLayout>
</template>
