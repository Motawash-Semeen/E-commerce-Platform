<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';

const props = defineProps({
    subcategories: { type: Object, required: true },  // paginator of { id, name, slug, category, products_count }
    categories: { type: Array, required: true },      // [{ id, name }] for the filter
    filters: { type: Object, required: true },
});

const category = ref(props.filters.category ?? '');

// Changing the category filter reloads the list, keeping the search term.
watch(category, (value) => {
    router.get(
        route('admin.subcategories.index'),
        { search: props.filters.search || undefined, category: value || undefined },
        { preserveState: true, replace: true },
    );
});

function destroy(subcategory) {
    if (confirm(`Delete the subcategory "${subcategory.name}"?`)) {
        router.delete(route('admin.subcategories.destroy', subcategory.slug), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Subcategories" />

    <AdminLayout>
        <template #header>
            <PageHeader title="Subcategories" subtitle="Second-level groups inside each category.">
                <Link :href="route('admin.subcategories.create')" class="btn-primary">+ New subcategory</Link>
            </PageHeader>
        </template>

        <div class="card overflow-hidden">
            <div class="flex flex-col gap-3 border-b border-gray-200 p-4 sm:flex-row">
                <SearchInput :model-value="filters.search" :extra="{ category: category || undefined }" placeholder="Search subcategories..." />
                <select v-model="category" aria-label="Filter by category" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-56 sm:text-sm">
                    <option value="">All categories</option>
                    <option v-for="option in categories" :key="option.id" :value="option.id">{{ option.name }}</option>
                </select>
            </div>

            <div v-if="subcategories.data.length" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-th">Name</th>
                            <th class="table-th">Category</th>
                            <th class="table-th">Products</th>
                            <th class="table-th text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="subcategory in subcategories.data" :key="subcategory.id" class="hover:bg-gray-50">
                            <td class="table-td font-medium text-gray-900">{{ subcategory.name }}</td>
                            <td class="table-td">
                                <span class="badge bg-indigo-50 text-indigo-700">{{ subcategory.category }}</span>
                            </td>
                            <td class="table-td">{{ subcategory.products_count }}</td>
                            <td class="table-td text-right">
                                <Link :href="route('admin.subcategories.edit', subcategory.slug)" class="btn-secondary btn-sm">Edit</Link>
                                <button type="button" class="btn-sm btn ml-2 text-red-600 hover:bg-red-50" @click="destroy(subcategory)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else title="No subcategories found" message="Try a different search or filter, or create one." />

            <div class="border-t border-gray-200 p-4" v-if="subcategories.last_page > 1">
                <Pagination :paginator="subcategories" />
            </div>
        </div>
    </AdminLayout>
</template>
