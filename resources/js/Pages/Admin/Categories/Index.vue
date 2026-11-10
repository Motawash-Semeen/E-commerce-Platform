<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';

defineProps({
    categories: { type: Object, required: true },  // paginator of { id, name, slug, subcategories_count, products_count }
    filters: { type: Object, required: true },
});

function destroy(category) {
    if (confirm(`Delete the category "${category.name}"?`)) {
        router.delete(route('admin.categories.destroy', category.slug), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Categories" />

    <AdminLayout>
        <template #header>
            <PageHeader title="Categories" subtitle="Top-level groups for the catalog.">
                <Link :href="route('admin.categories.create')" class="btn-primary">+ New category</Link>
            </PageHeader>
        </template>

        <div class="card overflow-hidden">
            <div class="border-b border-gray-200 p-4">
                <SearchInput :model-value="filters.search" placeholder="Search categories..." />
            </div>

            <div v-if="categories.data.length" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-th">Name</th>
                            <th class="table-th">Subcategories</th>
                            <th class="table-th">Products</th>
                            <th class="table-th text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="category in categories.data" :key="category.id" class="hover:bg-gray-50">
                            <td class="table-td font-medium text-gray-900">{{ category.name }}</td>
                            <td class="table-td">{{ category.subcategories_count }}</td>
                            <td class="table-td">{{ category.products_count }}</td>
                            <td class="table-td text-right">
                                <Link :href="route('admin.categories.edit', category.slug)" class="btn-secondary btn-sm">Edit</Link>
                                <button type="button" class="btn-sm btn ml-2 text-red-600 hover:bg-red-50" @click="destroy(category)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else title="No categories found" :message="filters.search ? 'Try a different search.' : 'Create the first category to get started.'" />

            <div class="border-t border-gray-200 p-4" v-if="categories.last_page > 1">
                <Pagination :paginator="categories" />
            </div>
        </div>
    </AdminLayout>
</template>
