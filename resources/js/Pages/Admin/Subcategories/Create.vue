<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import SubcategoryForm from './Partials/SubcategoryForm.vue';

defineProps({
    categories: { type: Array, required: true },
});
</script>

<template>
    <Head title="New subcategory" />

    <AdminLayout>
        <template #header>
            <PageHeader :back-href="route('admin.subcategories.index')" back-label="Subcategories" title="New subcategory" />
        </template>

        <SubcategoryForm :categories="categories" />
    </AdminLayout>
</template>
