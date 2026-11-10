<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({
    owners: { type: Object, required: true },  // paginator of { id, name, email, is_active, shop }
    filters: { type: Object, required: true },
});

function destroy(owner) {
    if (confirm(`Delete the account of "${owner.name}"?`)) {
        router.delete(route('admin.shop-owners.destroy', owner.id), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Shop owners" />

    <AdminLayout>
        <template #header>
            <PageHeader title="Shop owners" subtitle="Accounts that can log in and run a shop.">
                <Link :href="route('admin.shop-owners.create')" class="btn-primary">+ New shop owner</Link>
            </PageHeader>
        </template>

        <div class="card overflow-hidden">
            <div class="border-b border-gray-200 p-4">
                <SearchInput :model-value="filters.search" placeholder="Search by name or email..." />
            </div>

            <div v-if="owners.data.length" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-th">Name</th>
                            <th class="table-th">Email</th>
                            <th class="table-th">Shop</th>
                            <th class="table-th">Status</th>
                            <th class="table-th text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="owner in owners.data" :key="owner.id" class="hover:bg-gray-50">
                            <td class="table-td">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-indigo-50 text-sm font-bold text-indigo-700">
                                        {{ owner.name.charAt(0).toUpperCase() }}
                                    </span>
                                    <span class="font-medium text-gray-900">{{ owner.name }}</span>
                                </div>
                            </td>
                            <td class="table-td">{{ owner.email }}</td>
                            <td class="table-td">
                                <span v-if="owner.shop" class="font-medium text-gray-900">{{ owner.shop }}</span>
                                <span v-else class="text-gray-400">No shop</span>
                            </td>
                            <td class="table-td"><StatusBadge :active="owner.is_active" /></td>
                            <td class="table-td text-right">
                                <Link :href="route('admin.shop-owners.edit', owner.id)" class="btn-secondary btn-sm">Edit</Link>
                                <button type="button" class="btn-sm btn ml-2 text-red-600 hover:bg-red-50" @click="destroy(owner)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else title="No shop owners found" :message="filters.search ? 'Try a different search.' : 'Create an account, then give it a shop.'" />

            <div class="border-t border-gray-200 p-4" v-if="owners.last_page > 1">
                <Pagination :paginator="owners" />
            </div>
        </div>
    </AdminLayout>
</template>
