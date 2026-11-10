<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { formatPrice } from '@/utils/format';

defineProps({
    products: { type: Object, required: true },  // paginator (see ProductController@index)
    filters: { type: Object, required: true },
});

function destroy(product) {
    if (confirm(`Delete "${product.name}" from the catalog?`)) {
        router.delete(route('admin.products.destroy', product.slug), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Products" />

    <AdminLayout>
        <template #header>
            <PageHeader title="Catalog products" subtitle="The shared catalog. Every shop owner can list these in their shop.">
                <Link :href="route('admin.products.create')" class="btn-primary">+ New product</Link>
            </PageHeader>
        </template>

        <div class="card overflow-hidden">
            <div class="border-b border-gray-200 p-4">
                <SearchInput :model-value="filters.search" placeholder="Search products..." />
            </div>

            <div v-if="products.data.length" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-th">Product</th>
                            <th class="table-th">Category</th>
                            <th class="table-th">Created by</th>
                            <th class="table-th">In shops</th>
                            <th class="table-th">Suggested price</th>
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
                            <td class="table-td">
                                {{ product.category }} <span class="text-gray-400">›</span> {{ product.subcategory }}
                            </td>
                            <td class="table-td">
                                <span v-if="!product.creator" class="text-gray-400">Unknown</span>
                                <span v-else-if="product.creator_is_admin" class="badge bg-purple-50 text-purple-700">Admin</span>
                                <span v-else>{{ product.creator }}</span>
                            </td>
                            <td class="table-td">
                                <span class="badge" :class="product.listings_count ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-500'">
                                    {{ product.listings_count }} {{ product.listings_count === 1 ? 'shop' : 'shops' }}
                                </span>
                            </td>
                            <td class="table-td">
                                <span class="font-semibold text-gray-900">{{ formatPrice(product.new_price) }}</span>
                                <span v-if="product.old_price && Number(product.old_price) > Number(product.new_price)" class="ml-1 text-xs text-gray-400 line-through">{{ formatPrice(product.old_price) }}</span>
                            </td>
                            <td class="table-td text-right">
                                <Link :href="route('admin.products.edit', product.slug)" class="btn-secondary btn-sm">Edit</Link>
                                <button
                                    type="button"
                                    class="btn-sm btn ml-2 text-red-600 hover:bg-red-50"
                                    :title="product.listings_count ? 'Listed in a shop: remove it from all shops first' : 'Delete'"
                                    @click="destroy(product)"
                                >Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else title="No products found" :message="filters.search ? 'Try a different search.' : 'Add the first catalog product.'" />

            <div class="border-t border-gray-200 p-4" v-if="products.last_page > 1">
                <Pagination :paginator="products" />
            </div>
        </div>
    </AdminLayout>
</template>
