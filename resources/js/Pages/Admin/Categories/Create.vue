<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import CategoryForm from './Partials/CategoryForm.vue';
</script>

<template>
    <Head title="New category" />

    <AdminLayout>
        <template #header>
            <PageHeader :back-href="route('admin.categories.index')" back-label="Categories" title="New category" />
        </template>

        <CategoryForm />
    </AdminLayout>
</template>
