<script setup>
import { Head, Link } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import EditLink from '@/Components/EditLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ResourceTable from '@/Components/ResourceTable.vue';
import SearchInput from '@/Components/SearchInput.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { formatPrice } from '@/utils/format';

defineProps({
    listings: { type: Object, required: true },  // paginator of { id, name, category, image_url, price, old_price, is_active }
    filters: { type: Object, required: true },
    shop: { type: Object, required: true },      // { name, slug, is_active }
});
</script>

<template>
    <Head title="My listings" />

    <OwnerLayout>
        <template #header>
            <PageHeader title="My listings" :count="listings.total" :subtitle="`Products for sale in ${shop.name}, at your own prices.`">
                <Link v-if="shop.is_active" :href="route('shops.show', shop.slug)" class="btn-secondary">View storefront</Link>
                <Link :href="route('owner.catalog.index')" class="btn-primary">+ Add from catalog</Link>
            </PageHeader>
        </template>

        <ResourceTable :paginator="listings" empty-title="Your shop has no products yet" empty-message="Browse the shared catalog and add products at your own price.">
            <template #filters>
                <SearchInput :model-value="filters.search" placeholder="Search my listings..." />
            </template>

            <template #head>
                <th class="table-th">Product</th>
                <th class="table-th">My price</th>
                <th class="table-th">Status</th>
                <th class="table-th text-right">Actions</th>
            </template>

            <template #row="{ item }">
                <td class="table-td">
                    <div class="flex items-center gap-3">
                        <img :src="item.image_url" alt="" class="h-12 w-12 rounded-xl border border-gray-200 object-cover shadow-sm transition group-hover:scale-110" />
                        <div class="min-w-0">
                            <p class="max-w-xs truncate font-semibold text-gray-900">{{ item.name }}</p>
                            <p class="text-xs text-gray-500">{{ item.category }}</p>
                        </div>
                    </div>
                </td>
                <td class="table-td">
                    <span class="font-semibold text-gray-900">{{ formatPrice(item.price) }}</span>
                    <span v-if="item.old_price && Number(item.old_price) > Number(item.price)" class="ml-1 text-xs text-gray-400 line-through">{{ formatPrice(item.old_price) }}</span>
                </td>
                <td class="table-td"><StatusBadge :active="item.is_active" on="Visible" off="Hidden" /></td>
                <td class="table-td text-right">
                    <EditLink :href="route('owner.listings.edit', item.id)" />
                    <DeleteButton
                        :url="route('owner.listings.destroy', item.id)"
                        :title="`Remove ${item.name}?`"
                        message="It's removed from your shop only. The product stays in the catalog."
                        label="Remove"
                    />
                </td>
            </template>

            <template #empty>
                <Link :href="route('owner.catalog.index')" class="btn-primary">Browse catalog</Link>
            </template>
        </ResourceTable>
    </OwnerLayout>
</template>
