<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ShopLogo from '@/Components/ShopLogo.vue';
import { discountPercent, formatPrice } from '@/utils/format';

const props = defineProps({
    shop: { type: Object, required: true },        // { name, slug, logo_url }
    product: { type: Object, required: true },     // this shop's listing: { name, slug, price, old_price, description, image_url, category, subcategory }
    elsewhere: { type: Array, required: true },    // [{ shop: { name, slug }, price }] same product in other shops
});

const discount = computed(() => discountPercent(props.product.price, props.product.old_price));
</script>

<template>
    <Head :title="`${product.name} | ${shop.name}`" />

    <PublicLayout>
        <nav class="flex flex-wrap items-center gap-2 text-sm text-gray-500" aria-label="Breadcrumb">
            <Link :href="route('home')" class="hover:text-indigo-600">Shops</Link>
            <span aria-hidden="true">/</span>
            <Link :href="route('shops.show', shop.slug)" class="hover:text-indigo-600">{{ shop.name }}</Link>
            <span aria-hidden="true">/</span>
            <span class="truncate text-gray-700">{{ product.name }}</span>
        </nav>

        <div class="card mt-4 grid overflow-hidden md:grid-cols-2">
            <div class="relative bg-gray-100">
                <img :src="product.image_url" :alt="product.name" class="aspect-square h-full w-full object-cover" />
                <span v-if="discount" class="badge absolute left-4 top-4 bg-rose-500 px-3 py-1 text-sm text-white shadow">-{{ discount }}%</span>
            </div>

            <div class="flex flex-col p-6 sm:p-10">
                <p class="text-sm font-medium text-indigo-600">{{ product.category }} › {{ product.subcategory }}</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">{{ product.name }}</h1>

                <div class="mt-5 flex items-baseline gap-3">
                    <span class="text-3xl font-bold text-gray-900">{{ formatPrice(product.price) }}</span>
                    <span v-if="discount" class="text-lg text-gray-400 line-through">{{ formatPrice(product.old_price) }}</span>
                </div>

                <!-- pre-line keeps the line breaks; {{ }} escapes the text (no v-html). -->
                <p class="mt-6 whitespace-pre-line leading-relaxed text-gray-600">{{ product.description }}</p>

                <Link :href="route('shops.show', shop.slug)" class="mt-8 flex items-center gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-indigo-200 hover:bg-indigo-50/40">
                    <ShopLogo :name="shop.name" :url="shop.logo_url" size="h-10 w-10 text-base" />
                    <span class="min-w-0">
                        <span class="block text-xs text-gray-500">Sold by</span>
                        <span class="block truncate font-semibold text-gray-900">{{ shop.name }}</span>
                    </span>
                    <span class="ml-auto text-sm font-semibold text-indigo-600">Visit shop →</span>
                </Link>

                <div v-if="elsewhere.length" class="mt-6">
                    <h2 class="text-sm font-semibold text-gray-900">Also sold by</h2>
                    <ul class="mt-2 divide-y divide-gray-100 rounded-xl border border-gray-200">
                        <li v-for="other in elsewhere" :key="other.shop.slug">
                            <Link :href="route('shops.products.show', [other.shop.slug, product.slug])" class="flex items-center justify-between px-4 py-3 text-sm hover:bg-gray-50">
                                <span class="font-medium text-gray-700">{{ other.shop.name }}</span>
                                <span class="font-semibold text-gray-900">{{ formatPrice(other.price) }}</span>
                            </Link>
                        </li>
                    </ul>
                </div>

                <Link :href="route('shops.show', shop.slug)" class="mt-8 text-sm font-medium text-gray-500 hover:text-indigo-600">← Back to {{ shop.name }}</Link>
            </div>
        </div>
    </PublicLayout>
</template>
