<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import EditLink from '@/Components/EditLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ResourceTable from '@/Components/ResourceTable.vue';
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
</script>

<template>
    <Head title="Subcategories" />

    <AdminLayout>
        <template #header>
            <PageHeader title="Subcategories" :count="subcategories.total" subtitle="Second-level groups inside each category.">
                <Link :href="route('admin.subcategories.create')" class="btn-primary">+ New subcategory</Link>
            </PageHeader>
        </template>

        <ResourceTable :paginator="subcategories" empty-title="No subcategories found" empty-message="Try a different search or filter, or create one.">
            <template #filters>
                <SearchInput :model-value="filters.search" :extra="{ category: category || undefined }" placeholder="Search subcategories..." />
                <select v-model="category" aria-label="Filter by category" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-56 sm:text-sm">
                    <option value="">All categories</option>
                    <option v-for="option in categories" :key="option.id" :value="option.id">{{ option.name }}</option>
                </select>
            </template>

            <template #head>
                <th class="table-th">Subcategory</th>
                <th class="table-th">Category</th>
                <th class="table-th">Products</th>
                <th class="table-th text-right">Actions</th>
            </template>

            <template #row="{ item }">
                <td class="table-td">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600 transition group-hover:scale-110">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                        </span>
                        <span class="font-semibold text-gray-900">{{ item.name }}</span>
                    </div>
                </td>
                <td class="table-td"><span class="badge bg-indigo-50 text-indigo-700">{{ item.category }}</span></td>
                <td class="table-td"><span class="badge bg-sky-50 text-sky-700">{{ item.products_count }}</span></td>
                <td class="table-td text-right">
                    <EditLink :href="route('admin.subcategories.edit', item.slug)" />
                    <DeleteButton
                        :url="route('admin.subcategories.destroy', item.slug)"
                        :title="`Delete ${item.name}?`"
                        message="Only subcategories without products can be deleted. This cannot be undone."
                    />
                </td>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
