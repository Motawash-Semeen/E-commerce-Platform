<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import SubcategoryForm from './Partials/SubcategoryForm.vue';

defineProps({
    subcategory: { type: Object, required: true },  // { id, name, slug, category_id }
    categories: { type: Array, required: true },
});
</script>

<template>
    <Head title="Edit subcategory" />

    <AdminLayout>
        <template #header>
            <PageHeader :back-href="route('admin.subcategories.index')" back-label="Subcategories" :title="`Edit: ${subcategory.name}`" />
        </template>

        <SubcategoryForm :subcategory="subcategory" :categories="categories" />
    </AdminLayout>
</template>
