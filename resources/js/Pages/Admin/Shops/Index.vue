<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import EditLink from '@/Components/EditLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ResourceTable from '@/Components/ResourceTable.vue';
import SearchInput from '@/Components/SearchInput.vue';
import ShopLogo from '@/Components/ShopLogo.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({
    shops: { type: Object, required: true },  // paginator of { id, name, slug, logo_url, is_active, owner: {name, email}, listings_count }
    filters: { type: Object, required: true },
});
</script>

<template>
    <Head title="Shops" />

    <AdminLayout>
        <template #header>
            <PageHeader title="Shops" :count="shops.total" subtitle="Each shop belongs to one shop owner.">
                <Link :href="route('admin.shops.create')" class="btn-primary">+ New shop</Link>
            </PageHeader>
        </template>

        <ResourceTable
            :paginator="shops"
            empty-title="No shops found"
            :empty-message="filters.search ? 'Try a different search.' : 'Create a shop owner account first, then a shop for them.'"
        >
            <template #filters>
                <SearchInput :model-value="filters.search" placeholder="Search shops..." />
            </template>

            <template #head>
                <th class="table-th">Shop</th>
                <th class="table-th">Owner</th>
                <th class="table-th">Listings</th>
                <th class="table-th">Status</th>
                <th class="table-th text-right">Actions</th>
            </template>

            <template #row="{ item }">
                <td class="table-td">
                    <div class="flex items-center gap-3">
                        <ShopLogo :name="item.name" :url="item.logo_url" size="h-10 w-10 text-base" class="transition group-hover:scale-110" />
                        <div>
                            <p class="font-semibold text-gray-900">{{ item.name }}</p>
                            <Link v-if="item.is_active" :href="route('shops.show', item.slug)" class="text-xs font-medium text-indigo-600 hover:underline">View storefront</Link>
                            <p v-else class="text-xs text-gray-400">Hidden from customers</p>
                        </div>
                    </div>
                </td>
                <td class="table-td">
                    <p class="text-gray-900">{{ item.owner.name }}</p>
                    <p class="text-xs text-gray-500">{{ item.owner.email }}</p>
                </td>
                <td class="table-td"><span class="badge bg-sky-50 text-sky-700">{{ item.listings_count }}</span></td>
                <td class="table-td"><StatusBadge :active="item.is_active" /></td>
                <td class="table-td text-right">
                    <EditLink :href="route('admin.shops.edit', item.slug)" />
                    <DeleteButton
                        :url="route('admin.shops.destroy', item.slug)"
                        :title="`Delete ${item.name}?`"
                        :message="`Its ${item.listings_count} listing(s) are removed too. Catalog products stay. This cannot be undone.`"
                    />
                </td>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
