<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import ListingCard from '@/Components/ListingCard.vue';
import ShopLogo from '@/Components/ShopLogo.vue';

defineProps({
    shops: { type: Array, required: true },   // [{ name, slug, description, logo_url, products_count }]
    latest: { type: Array, required: true },  // latest visible listings, each with { shop: { name, slug } }
});
</script>

<template>
    <Head title="Shops" />

    <PublicLayout>
        <!-- Hero -->
        <section class="relative mb-12 overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-600 px-6 py-14 text-white shadow-lg sm:px-12 sm:py-20">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-2xl" aria-hidden="true" />
            <div class="absolute -bottom-20 left-10 h-56 w-56 rounded-full bg-purple-300/20 blur-2xl" aria-hidden="true" />
            <div class="relative max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-widest text-indigo-100">Many shops, one place</p>
                <h1 class="mt-3 text-4xl font-extrabold tracking-tight sm:text-5xl">Discover independent shops</h1>
                <p class="mt-4 text-lg text-indigo-100">Every shop sets its own prices. Browse a shop to see what it sells today.</p>
                <a href="#shops" class="mt-8 inline-flex items-center gap-2 rounded-lg bg-white px-5 py-3 text-sm font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-50">
                    Browse shops
                    <span aria-hidden="true">↓</span>
                </a>
            </div>
        </section>

        <!-- Shops -->
        <section id="shops" class="scroll-mt-24">
            <div class="mb-6 flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">Shops</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ shops.length }} {{ shops.length === 1 ? 'shop' : 'shops' }} open now</p>
                </div>
            </div>

            <div v-if="shops.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="shop in shops"
                    :key="shop.slug"
                    :href="route('shops.show', shop.slug)"
                    class="card group flex flex-col p-6 transition hover:-translate-y-0.5 hover:shadow-lg"
                >
                    <div class="flex items-center gap-4">
                        <ShopLogo :name="shop.name" :url="shop.logo_url" />
                        <div class="min-w-0">
                            <h3 class="truncate text-lg font-semibold text-gray-900 group-hover:text-indigo-700">{{ shop.name }}</h3>
                            <p class="text-sm text-gray-500">{{ shop.products_count }} {{ shop.products_count === 1 ? 'product' : 'products' }}</p>
                        </div>
                    </div>
                    <p v-if="shop.description" class="mt-4 line-clamp-3 text-sm text-gray-600">{{ shop.description }}</p>
                    <span class="mt-auto pt-5 text-sm font-semibold text-indigo-600">Visit shop →</span>
                </Link>
            </div>
            <div v-else class="card">
                <EmptyState title="No shops are open yet" message="Please check back soon." />
            </div>
        </section>

        <!-- Latest products -->
        <section v-if="latest.length" class="mt-14">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">New in the shops</h2>
            <p class="mt-1 text-sm text-gray-500">The latest products added by our shops.</p>

            <div class="mt-6 grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 lg:grid-cols-4">
                <ListingCard
                    v-for="listing in latest"
                    :key="`${listing.shop.slug}-${listing.slug}`"
                    :listing="listing"
                    :shop-slug="listing.shop.slug"
                    show-shop
                />
            </div>
        </section>
    </PublicLayout>
</template>
