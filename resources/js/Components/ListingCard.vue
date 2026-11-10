<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { discountPercent, formatPrice } from '@/utils/format';

// A product card on the public pages, with the shop's own price and image.
const props = defineProps({
    listing: { type: Object, required: true },  // { name, slug, price, old_price, image_url, category, subcategory, shop? }
    shopSlug: { type: String, required: true },
    showShop: { type: Boolean, default: false },
});

const discount = computed(() => discountPercent(props.listing.price, props.listing.old_price));
</script>

<template>
    <Link
        :href="route('shops.products.show', [shopSlug, listing.slug])"
        class="card group flex flex-col overflow-hidden transition hover:-translate-y-0.5 hover:shadow-lg"
    >
        <div class="relative aspect-square overflow-hidden bg-gray-100">
            <img :src="listing.image_url" :alt="listing.name" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
            <span v-if="discount" class="badge absolute left-3 top-3 bg-rose-500 text-white shadow-sm">-{{ discount }}%</span>
        </div>
        <div class="flex flex-1 flex-col p-4">
            <p v-if="showShop && listing.shop" class="text-xs font-semibold uppercase tracking-wider text-indigo-600">{{ listing.shop.name }}</p>
            <p v-else class="text-xs text-gray-500">{{ listing.subcategory }}</p>
            <h3 class="mt-1 line-clamp-2 font-semibold text-gray-900 group-hover:text-indigo-700">{{ listing.name }}</h3>
            <div class="mt-auto flex flex-wrap items-baseline gap-x-2 pt-3">
                <span class="whitespace-nowrap text-base font-bold text-gray-900 sm:text-lg">{{ formatPrice(listing.price) }}</span>
                <span v-if="discount" class="whitespace-nowrap text-xs text-gray-400 line-through sm:text-sm">{{ formatPrice(listing.old_price) }}</span>
            </div>
        </div>
    </Link>
</template>
