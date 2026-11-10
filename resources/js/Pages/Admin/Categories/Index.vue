<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import EditLink from '@/Components/EditLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ResourceTable from '@/Components/ResourceTable.vue';
import SearchInput from '@/Components/SearchInput.vue';

defineProps({
    categories: { type: Object, required: true },  // paginator of { id, name, slug, subcategories_count, products_count }
    filters: { type: Object, required: true },
});
</script>

<template>
    <Head title="Categories" />

    <AdminLayout>
        <template #header>
            <PageHeader title="Categories" :count="categories.total" subtitle="Top-level groups for the catalog.">
                <Link :href="route('admin.categories.create')" class="btn-primary">+ New category</Link>
            </PageHeader>
        </template>

        <ResourceTable
            :paginator="categories"
            empty-title="No categories found"
            :empty-message="filters.search ? 'Try a different search.' : 'Create the first category to get started.'"
        >
            <template #filters>
                <SearchInput :model-value="filters.search" placeholder="Search categories..." />
            </template>

            <template #head>
                <th class="table-th">Category</th>
                <th class="table-th">Subcategories</th>
                <th class="table-th">Products</th>
                <th class="table-th text-right">Actions</th>
            </template>

            <template #row="{ item }">
                <td class="table-td">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 font-bold text-white shadow-sm transition group-hover:scale-110">
                            {{ item.name.charAt(0) }}
                        </span>
                        <span class="font-semibold text-gray-900">{{ item.name }}</span>
                    </div>
                </td>
                <td class="table-td"><span class="badge bg-violet-50 text-violet-700">{{ item.subcategories_count }}</span></td>
                <td class="table-td"><span class="badge bg-sky-50 text-sky-700">{{ item.products_count }}</span></td>
                <td class="table-td text-right">
                    <EditLink :href="route('admin.categories.edit', item.slug)" />
                    <DeleteButton
                        :url="route('admin.categories.destroy', item.slug)"
                        :title="`Delete ${item.name}?`"
                        message="Only empty categories can be deleted. This cannot be undone."
                    />
                </td>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
