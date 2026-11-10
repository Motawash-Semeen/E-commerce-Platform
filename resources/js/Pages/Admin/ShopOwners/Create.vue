<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ShopOwnerForm from './Partials/ShopOwnerForm.vue';
</script>

<template>
    <Head title="New shop owner" />

    <AdminLayout>
        <template #header>
            <PageHeader title="New shop owner" subtitle="They can log in once the account is active. Assign a shop under Shops." />
        </template>

        <ShopOwnerForm />
    </AdminLayout>
</template>
