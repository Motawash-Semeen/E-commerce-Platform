<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({
    shops: { type: Object, required: true },  // paginator of { id, name, slug, logo_url, is_active, owner: {name, email}, listings_count }
    filters: { type: Object, required: true },
});

function destroy(shop) {
    if (confirm(`Delete the shop "${shop.name}"? Its ${shop.listings_count} listing(s) will be removed too.`)) {
        router.delete(route('admin.shops.destroy', shop.slug), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Shops" />

    <AdminLayout>
        <template #header>
            <PageHeader title="Shops" subtitle="Each shop belongs to one shop owner.">
                <Link :href="route('admin.shops.create')" class="btn-primary">+ New shop</Link>
            </PageHeader>
        </template>

        <div class="card overflow-hidden">
            <div class="border-b border-gray-200 p-4">
                <SearchInput :model-value="filters.search" placeholder="Search shops..." />
            </div>

            <div v-if="shops.data.length" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-th">Shop</th>
                            <th class="table-th">Owner</th>
                            <th class="table-th">Listings</th>
                            <th class="table-th">Status</th>
                            <th class="table-th text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="shop in shops.data" :key="shop.id" class="hover:bg-gray-50">
                            <td class="table-td">
                                <div class="flex items-center gap-3">
                                    <img v-if="shop.logo_url" :src="shop.logo_url" alt="" class="h-10 w-10 rounded-xl border border-gray-200 object-cover" />
                                    <span v-else class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 font-bold text-white">
                                        {{ shop.name.charAt(0).toUpperCase() }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ shop.name }}</p>
                                        <Link v-if="shop.is_active" :href="route('shops.show', shop.slug)" class="text-xs text-indigo-600 hover:underline">View storefront</Link>
                                    </div>
                                </div>
                            </td>
                            <td class="table-td">
                                <p class="text-gray-900">{{ shop.owner.name }}</p>
                                <p class="text-xs text-gray-500">{{ shop.owner.email }}</p>
                            </td>
                            <td class="table-td">{{ shop.listings_count }}</td>
                            <td class="table-td"><StatusBadge :active="shop.is_active" /></td>
                            <td class="table-td text-right">
                                <Link :href="route('admin.shops.edit', shop.slug)" class="btn-secondary btn-sm">Edit</Link>
                                <button type="button" class="btn-sm btn ml-2 text-red-600 hover:bg-red-50" @click="destroy(shop)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else title="No shops found" :message="filters.search ? 'Try a different search.' : 'Create a shop owner account first, then a shop for them.'" />

            <div class="border-t border-gray-200 p-4" v-if="shops.last_page > 1">
                <Pagination :paginator="shops" />
            </div>
        </div>
    </AdminLayout>
</template>
