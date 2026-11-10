<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import EditLink from '@/Components/EditLink.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ResourceTable from '@/Components/ResourceTable.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { formatPrice } from '@/utils/format';

defineProps({
    products: { type: Object, required: true },  // paginator (see ProductController@index)
    filters: { type: Object, required: true },
});
</script>

<template>
    <Head title="Products" />

    <AdminLayout>
        <template #header>
            <PageHeader title="Catalog products" :count="products.total" subtitle="The shared catalog. Every shop owner can list these in their shop.">
                <Link :href="route('admin.products.create')" class="btn-primary">+ New product</Link>
            </PageHeader>
        </template>

        <ResourceTable
            :paginator="products"
            empty-title="No products found"
            :empty-message="filters.search ? 'Try a different search.' : 'Add the first catalog product.'"
        >
            <template #filters>
                <SearchInput :model-value="filters.search" placeholder="Search products..." />
            </template>

            <template #head>
                <th class="table-th">Product</th>
                <th class="table-th">Created by</th>
                <th class="table-th">In shops</th>
                <th class="table-th">Suggested price</th>
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
                <td class="table-td">
                    <span v-if="!item.creator" class="text-gray-400">Unknown</span>
                    <span v-else-if="item.creator_is_admin" class="badge bg-violet-50 text-violet-700">Admin</span>
                    <span v-else class="badge bg-gray-100 text-gray-700">{{ item.creator }}</span>
                </td>
                <td class="table-td">
                    <span class="badge" :class="item.listings_count ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500'">
                        {{ item.listings_count }} {{ item.listings_count === 1 ? 'shop' : 'shops' }}
                    </span>
                </td>
                <td class="table-td">
                    <span class="font-semibold text-gray-900">{{ formatPrice(item.new_price) }}</span>
                    <span v-if="item.old_price && Number(item.old_price) > Number(item.new_price)" class="ml-1 text-xs text-gray-400 line-through">{{ formatPrice(item.old_price) }}</span>
                </td>
                <td class="table-td text-right">
                    <EditLink :href="route('admin.products.edit', item.slug)" />
                    <DeleteButton
                        :url="route('admin.products.destroy', item.slug)"
                        :title="`Delete ${item.name}?`"
                        :message="item.listings_count ? 'This product is listed in a shop, so it can\'t be deleted. Remove it from all shops first.' : 'It is removed from the catalog. This cannot be undone.'"
                    />
                </td>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
