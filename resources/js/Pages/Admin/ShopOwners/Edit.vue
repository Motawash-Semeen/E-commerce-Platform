<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ShopOwnerForm from './Partials/ShopOwnerForm.vue';

defineProps({
    owner: { type: Object, required: true },  // { id, name, email, is_active }
});
</script>

<template>
    <Head title="Edit shop owner" />

    <AdminLayout>
        <template #header>
            <PageHeader :back-href="route('admin.shop-owners.index')" back-label="Shop owners" :title="`Edit: ${owner.name}`" />
        </template>

        <ShopOwnerForm :owner="owner" />
    </AdminLayout>
</template>
