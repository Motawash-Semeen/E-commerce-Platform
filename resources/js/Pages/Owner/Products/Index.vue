<script setup>
import { Head, Link } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
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
            <PageHeader title="My products" subtitle="Catalog products you created. Every shop owner can see and list them.">
                <Link :href="route('owner.products.create')" class="btn-primary">+ Create product</Link>
            </PageHeader>
        </template>

        <div class="card overflow-hidden">
            <div class="border-b border-gray-200 p-4">
                <SearchInput :model-value="filters.search" placeholder="Search my products..." />
            </div>

            <div v-if="products.data.length" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-th">Product</th>
                            <th class="table-th">Category</th>
                            <th class="table-th">Suggested price</th>
                            <th class="table-th">Sold in</th>
                            <th class="table-th text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50">
                            <td class="table-td">
                                <div class="flex items-center gap-3">
                                    <img :src="product.image_url" alt="" class="h-12 w-12 rounded-lg border border-gray-200 object-cover" />
                                    <span class="max-w-xs truncate font-medium text-gray-900">{{ product.name }}</span>
                                </div>
                            </td>
                            <td class="table-td">{{ product.category }} <span class="text-gray-400">›</span> {{ product.subcategory }}</td>
                            <td class="table-td font-semibold text-gray-900">{{ formatPrice(product.new_price) }}</td>
                            <td class="table-td">
                                {{ product.listings_count }} {{ product.listings_count === 1 ? 'shop' : 'shops' }}
                                <span v-if="product.in_my_shop" class="badge ml-1 bg-green-100 text-green-800">incl. mine</span>
                            </td>
                            <td class="table-td text-right">
                                <Link
                                    v-if="hasShop && !product.in_my_shop"
                                    :href="route('owner.catalog.add', product.slug)"
                                    class="btn-sm btn mr-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100"
                                >Add to my shop</Link>
                                <Link :href="route('owner.products.edit', product.slug)" class="btn-secondary btn-sm">Edit</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else title="You haven't created any products" message="Products you create join the shared catalog for every shop.">
                <Link :href="route('owner.products.create')" class="btn-primary">Create a product</Link>
            </EmptyState>

            <div class="border-t border-gray-200 p-4" v-if="products.last_page > 1">
                <Pagination :paginator="products" />
            </div>
        </div>
    </OwnerLayout>
</template>
