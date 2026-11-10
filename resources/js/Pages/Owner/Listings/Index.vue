<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { formatPrice } from '@/utils/format';

defineProps({
    listings: { type: Object, required: true },  // paginator of { id, name, category, image_url, price, old_price, is_active }
    filters: { type: Object, required: true },
    shop: { type: Object, required: true },      // { name, slug, is_active }
});

function destroy(listing) {
    if (confirm(`Remove "${listing.name}" from your shop? It stays in the catalog.`)) {
        router.delete(route('owner.listings.destroy', listing.id), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="My listings" />

    <OwnerLayout>
        <template #header>
            <PageHeader :title="`My listings`" :subtitle="`Products for sale in ${shop.name}, at your own prices.`">
                <Link v-if="shop.is_active" :href="route('shops.show', shop.slug)" class="btn-secondary">View storefront</Link>
                <Link :href="route('owner.catalog.index')" class="btn-primary">+ Add from catalog</Link>
            </PageHeader>
        </template>

        <div class="card overflow-hidden">
            <div class="border-b border-gray-200 p-4">
                <SearchInput :model-value="filters.search" placeholder="Search my listings..." />
            </div>

            <div v-if="listings.data.length" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-th">Product</th>
                            <th class="table-th">My price</th>
                            <th class="table-th">Status</th>
                            <th class="table-th text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="listing in listings.data" :key="listing.id" class="hover:bg-gray-50">
                            <td class="table-td">
                                <div class="flex items-center gap-3">
                                    <img :src="listing.image_url" alt="" class="h-12 w-12 rounded-lg border border-gray-200 object-cover" />
                                    <div class="min-w-0">
                                        <p class="max-w-xs truncate font-medium text-gray-900">{{ listing.name }}</p>
                                        <p class="text-xs text-gray-500">{{ listing.category }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="table-td">
                                <span class="font-semibold text-gray-900">{{ formatPrice(listing.price) }}</span>
                                <span v-if="listing.old_price && Number(listing.old_price) > Number(listing.price)" class="ml-1 text-xs text-gray-400 line-through">{{ formatPrice(listing.old_price) }}</span>
                            </td>
                            <td class="table-td"><StatusBadge :active="listing.is_active" on="Visible" off="Hidden" /></td>
                            <td class="table-td text-right">
                                <Link :href="route('owner.listings.edit', listing.id)" class="btn-secondary btn-sm">Edit</Link>
                                <button type="button" class="btn-sm btn ml-2 text-red-600 hover:bg-red-50" @click="destroy(listing)">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else title="Your shop has no products yet" message="Browse the shared catalog and add products at your own price.">
                <Link :href="route('owner.catalog.index')" class="btn-primary">Browse catalog</Link>
            </EmptyState>

            <div class="border-t border-gray-200 p-4" v-if="listings.last_page > 1">
                <Pagination :paginator="listings" />
            </div>
        </div>
    </OwnerLayout>
</template>
