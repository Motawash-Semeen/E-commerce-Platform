<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import CategoryForm from './Partials/CategoryForm.vue';

defineProps({
    category: { type: Object, required: true },  // { id, name, slug }
});
</script>

<template>
    <Head title="Edit category" />

    <AdminLayout>
        <template #header>
            <PageHeader :back-href="route('admin.categories.index')" back-label="Categories" :title="`Edit: ${category.name}`" />
        </template>

        <CategoryForm :category="category" />
    </AdminLayout>
</template>
