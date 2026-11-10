<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { formatPrice } from '@/utils/format';

const props = defineProps({
    products: { type: Object, required: true },   // paginator (see CatalogController@index)
    categories: { type: Array, required: true },  // [{ id, name }]
    filters: { type: Object, required: true },
});

const category = ref(props.filters.category ?? '');

watch(category, (value) => {
    router.get(
        route('owner.catalog.index'),
        { search: props.filters.search || undefined, category: value || undefined },
        { preserveState: true, replace: true },
    );
});
</script>

<template>
    <Head title="Browse catalog" />

    <OwnerLayout>
        <template #header>
            <PageHeader title="Browse catalog" subtitle="Products from the admin and every shop owner. Add any of them to your shop at your own price.">
                <Link :href="route('owner.products.create')" class="btn-secondary">+ Create a product</Link>
            </PageHeader>
        </template>

        <div class="mb-6 flex flex-col gap-3 sm:flex-row">
            <SearchInput :model-value="filters.search" :extra="{ category: category || undefined }" placeholder="Search the catalog..." />
            <select v-model="category" aria-label="Filter by category" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-56 sm:text-sm">
                <option value="">All categories</option>
                <option v-for="option in categories" :key="option.id" :value="option.id">{{ option.name }}</option>
            </select>
        </div>

        <div v-if="products.data.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <div v-for="product in products.data" :key="product.id" class="card group flex flex-col overflow-hidden">
                <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                    <img :src="product.image_url" :alt="product.name" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" />
                    <span class="badge absolute left-3 top-3 bg-white/90 text-gray-700 shadow-sm">{{ product.category }}</span>
                </div>
                <div class="flex flex-1 flex-col p-4">
                    <p class="text-xs text-gray-500">{{ product.subcategory }}</p>
                    <h3 class="mt-1 line-clamp-2 font-semibold text-gray-900">{{ product.name }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Suggested <span class="font-semibold text-gray-900">{{ formatPrice(product.new_price) }}</span>
                    </p>
                    <div class="mt-auto pt-4">
                        <span v-if="product.in_my_shop" class="badge w-full justify-center bg-green-100 py-2 text-green-800">✓ Already in your shop</span>
                        <Link v-else :href="route('owner.catalog.add', product.slug)" class="btn-primary w-full">Add to my shop</Link>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="card">
            <EmptyState title="No products found" message="Try a different search or category, or create a new product." />
        </div>

        <div class="mt-6">
            <Pagination :paginator="products" />
        </div>
    </OwnerLayout>
</template>
