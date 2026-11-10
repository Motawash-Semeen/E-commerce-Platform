<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ShopForm from './Partials/ShopForm.vue';

defineProps({
    owners: { type: Array, required: true },
});
</script>

<template>
    <Head title="New shop" />

    <AdminLayout>
        <template #header>
            <PageHeader :back-href="route('admin.shops.index')" back-label="Shops" title="New shop" />
        </template>

        <ShopForm :owners="owners" />
    </AdminLayout>
</template>
