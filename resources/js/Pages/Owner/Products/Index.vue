<script setup>
import { Head, Link } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import EditLink from '@/Components/EditLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ResourceTable from '@/Components/ResourceTable.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { formatPrice } from '@/utils/format';

defineProps({
    products: { type: Object, required: true },  // paginator (see Owner\ProductController@index)
    filters: { type: Object, required: true },
    hasShop: { type: Boolean, required: true },
});
</script>

<template>
    <Head title="My products" />

    <OwnerLayout>
        <template #header>
            <PageHeader title="My products" :count="products.total" subtitle="Catalog products you created. Every shop owner can see and list them.">
                <Link :href="route('owner.products.create')" class="btn-primary">+ Create product</Link>
            </PageHeader>
        </template>

        <ResourceTable :paginator="products" empty-title="You haven't created any products" empty-message="Products you create join the shared catalog for every shop.">
            <template #filters>
                <SearchInput :model-value="filters.search" placeholder="Search my products..." />
            </template>

            <template #head>
                <th class="table-th">Product</th>
                <th class="table-th">Suggested price</th>
                <th class="table-th">Sold in</th>
                <th class="table-th text-right">Actions</th>
            </template>

            <template #row="{ item }">
                <td class="table-td">
                    <div class="flex items-center gap-3">
                        <img :src="item.image_url" alt="" class="h-12 w-12 rounded-xl border border-gray-200 object-cover shadow-sm transition group-hover:scale-110" />
                        <div class="min-w-0">
                            <p class="max-w-xs truncate font-semibold text-gray-900">{{ item.name }}</p>
                            <p class="text-xs text-gray-500">{{ item.category }} › {{ item.subcategory }}</p>
                        </div>
                    </div>
                </td>
                <td class="table-td font-semibold text-gray-900">{{ formatPrice(item.new_price) }}</td>
                <td class="table-td">
                    <span class="badge bg-sky-50 text-sky-700">{{ item.listings_count }} {{ item.listings_count === 1 ? 'shop' : 'shops' }}</span>
                    <span v-if="item.in_my_shop" class="badge ml-1 bg-emerald-50 text-emerald-700">incl. mine</span>
                </td>
                <td class="table-td text-right">
                    <Link
                        v-if="hasShop && !item.in_my_shop"
                        :href="route('owner.catalog.add', item.slug)"
                        class="btn-sm btn mr-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100"
                    >+ Add to my shop</Link>
                    <EditLink :href="route('owner.products.edit', item.slug)" />
                </td>
            </template>

            <template #empty>
                <Link :href="route('owner.products.create')" class="btn-primary">Create a product</Link>
            </template>
        </ResourceTable>
    </OwnerLayout>
</template>
