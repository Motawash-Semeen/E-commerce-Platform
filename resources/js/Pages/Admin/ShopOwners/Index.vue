<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import EditLink from '@/Components/EditLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ResourceTable from '@/Components/ResourceTable.vue';
import SearchInput from '@/Components/SearchInput.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({
    owners: { type: Object, required: true },  // paginator of { id, name, email, is_active, shop }
    filters: { type: Object, required: true },
});
</script>

<template>
    <Head title="Shop owners" />

    <AdminLayout>
        <template #header>
            <PageHeader title="Shop owners" :count="owners.total" subtitle="Accounts that can log in and run a shop.">
                <Link :href="route('admin.shop-owners.create')" class="btn-primary">+ New shop owner</Link>
            </PageHeader>
        </template>

        <ResourceTable
            :paginator="owners"
            empty-title="No shop owners found"
            :empty-message="filters.search ? 'Try a different search.' : 'Create an account, then give it a shop.'"
        >
            <template #filters>
                <SearchInput :model-value="filters.search" placeholder="Search by name or email..." />
            </template>

            <template #head>
                <th class="table-th">Owner</th>
                <th class="table-th">Shop</th>
                <th class="table-th">Status</th>
                <th class="table-th text-right">Actions</th>
            </template>

            <template #row="{ item }">
                <td class="table-td">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-sky-400 to-indigo-500 font-bold text-white shadow-sm transition group-hover:scale-110">
                            {{ item.name.charAt(0).toUpperCase() }}
                        </span>
                        <div>
                            <p class="font-semibold text-gray-900">{{ item.name }}</p>
                            <p class="text-xs text-gray-500">{{ item.email }}</p>
                        </div>
                    </div>
                </td>
                <td class="table-td">
                    <span v-if="item.shop" class="badge bg-indigo-50 text-indigo-700">{{ item.shop }}</span>
                    <span v-else class="badge bg-amber-50 text-amber-700">No shop</span>
                </td>
                <td class="table-td"><StatusBadge :active="item.is_active" /></td>
                <td class="table-td text-right">
                    <EditLink :href="route('admin.shop-owners.edit', item.id)" />
                    <DeleteButton
                        :url="route('admin.shop-owners.destroy', item.id)"
                        :title="`Delete ${item.name}?`"
                        message="Owners who still have a shop can't be deleted. This cannot be undone."
                    />
                </td>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
