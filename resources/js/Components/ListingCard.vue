<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { discountPercent, formatPrice } from '@/utils/format';

// A product card on the public pages, with the shop's own price and image.
const props = defineProps({
    listing: { type: Object, required: true },  // { name, slug, price, old_price, image_url, category, subcategory, shop? }
    shopSlug: { type: String, required: true },
    showShop: { type: Boolean, default: false },
    index: { type: Number, default: 0 },          // position in the grid, for the staggered entrance
});

const discount = computed(() => discountPercent(props.listing.price, props.listing.old_price));
</script>

<template>
    <Link
        :href="route('shops.products.show', [shopSlug, listing.slug])"
        class="card card-hover group flex animate-fade-up flex-col overflow-hidden"
        :style="{ animationDelay: `${index * 60}ms` }"
    >
        <div class="relative aspect-square overflow-hidden bg-gray-100">
            <img :src="listing.image_url" :alt="listing.name" loading="lazy" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/40 via-transparent to-transparent opacity-0 transition duration-300 group-hover:opacity-100" aria-hidden="true" />
            <span v-if="discount" class="badge absolute left-3 top-3 bg-gradient-to-r from-rose-500 to-pink-500 text-white shadow-md">-{{ discount }}%</span>
            <span class="absolute bottom-3 left-1/2 -translate-x-1/2 translate-y-3 whitespace-nowrap rounded-full bg-white px-4 py-1.5 text-xs font-semibold text-gray-900 opacity-0 shadow-lg transition duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                View details
            </span>
        </div>
        <div class="flex flex-1 flex-col p-4">
            <p v-if="showShop && listing.shop" class="text-xs font-semibold uppercase tracking-wider text-indigo-600">{{ listing.shop.name }}</p>
            <p v-else class="text-xs text-gray-500">{{ listing.subcategory }}</p>
            <h3 class="mt-1 line-clamp-2 font-semibold text-gray-900 transition group-hover:text-indigo-700">{{ listing.name }}</h3>
            <div class="mt-auto flex flex-wrap items-baseline gap-x-2 pt-3">
                <span class="whitespace-nowrap text-base font-bold text-gray-900 sm:text-lg">{{ formatPrice(listing.price) }}</span>
                <span v-if="discount" class="whitespace-nowrap text-xs text-gray-400 line-through sm:text-sm">{{ formatPrice(listing.old_price) }}</span>
            </div>
        </div>
    </Link>
</template>
