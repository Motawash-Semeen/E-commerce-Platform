<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import ListingCard from '@/Components/ListingCard.vue';
import ShopLogo from '@/Components/ShopLogo.vue';
import { formatPrice } from '@/utils/format';

const props = defineProps({
    shops: { type: Array, required: true },   // [{ name, slug, description, logo_url, products_count }]
    latest: { type: Array, required: true },  // latest visible listings, each with { shop: { name, slug } }
});

const productCount = computed(() => props.shops.reduce((sum, shop) => sum + shop.products_count, 0));

// Three real products float in the hero; each gets its own position, tilt and timing.
const floating = computed(() => props.latest.slice(0, 3).map((listing, i) => ({
    listing,
    style: { '--tilt': ['-6deg', '5deg', '-2deg'][i], animationDelay: `${i * -2}s` },
    position: ['left-0 top-0', 'right-0 top-12', 'left-1/3 bottom-0 z-10'][i],
})));
</script>

<template>
    <Head title="Shops" />

    <PublicLayout>
        <!-- Hero -->
        <section class="relative mb-16 overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-500 bg-[length:200%_200%] px-6 py-14 text-white shadow-2xl shadow-indigo-500/30 animate-gradient sm:px-12 lg:py-20">
            <div class="absolute -left-20 -top-20 h-72 w-72 rounded-full bg-white/10 blur-3xl animate-blob" aria-hidden="true" />
            <div class="absolute -bottom-24 right-1/3 h-72 w-72 rounded-full bg-fuchsia-300/20 blur-3xl animate-blob [animation-delay:-6s]" aria-hidden="true" />
            <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.12)_1px,transparent_1px)] [background-size:22px_22px]" aria-hidden="true" />

            <div class="relative grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <p class="inline-flex animate-fade-up items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-white ring-1 ring-white/25 backdrop-blur">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-300" aria-hidden="true" />
                        Many shops, one place
                    </p>
                    <h1 class="mt-5 animate-fade-up text-4xl font-extrabold leading-tight tracking-tight [animation-delay:100ms] sm:text-6xl">
                        Discover shops<br />you'll <span class="bg-gradient-to-r from-amber-200 to-pink-200 bg-clip-text text-transparent">love</span>
                    </h1>
                    <p class="mt-5 max-w-lg animate-fade-up text-lg text-indigo-100 [animation-delay:200ms]">
                        Independent shops, each with its own prices. Pick a shop and see what it's selling today.
                    </p>
                    <div class="mt-8 flex animate-fade-up flex-wrap items-center gap-4 [animation-delay:300ms]">
                        <a href="#shops" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-indigo-700 shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-[0.97]">
                            Browse shops <span aria-hidden="true">→</span>
                        </a>
                        <div class="flex gap-6 text-sm">
                            <p><span class="block text-2xl font-extrabold">{{ shops.length }}</span><span class="text-indigo-100">shops</span></p>
                            <p><span class="block text-2xl font-extrabold">{{ productCount }}</span><span class="text-indigo-100">products</span></p>
                        </div>
                    </div>
                </div>

                <!-- Floating product cards -->
                <div v-if="floating.length" class="relative hidden h-[27rem] lg:block" aria-hidden="true">
                    <Link
                        v-for="item in floating"
                        :key="item.listing.slug + item.listing.shop.slug"
                        :href="route('shops.products.show', [item.listing.shop.slug, item.listing.slug])"
                        tabindex="-1"
                        class="absolute w-52 animate-float rounded-2xl bg-white p-3 text-gray-900 shadow-2xl transition-shadow hover:shadow-indigo-900/40"
                        :class="item.position"
                        :style="item.style"
                    >
                        <img :src="item.listing.image_url" alt="" class="aspect-[4/3] w-full rounded-xl object-cover" />
                        <p class="mt-2 truncate text-sm font-semibold">{{ item.listing.name }}</p>
                        <p class="flex items-center justify-between text-xs text-gray-500">
                            {{ item.listing.shop.name }}
                            <span class="font-bold text-indigo-600">{{ formatPrice(item.listing.price) }}</span>
                        </p>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Shops -->
        <section id="shops" class="scroll-mt-24">
            <div class="mb-6">
                <p class="text-sm font-semibold uppercase tracking-widest text-indigo-600">Open now</p>
                <h2 class="mt-1 text-3xl font-extrabold tracking-tight text-gray-900">Explore our <span class="text-gradient">shops</span></h2>
            </div>

            <div v-if="shops.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="(shop, i) in shops"
                    :key="shop.slug"
                    :href="route('shops.show', shop.slug)"
                    class="card card-hover group relative flex animate-fade-up flex-col overflow-hidden p-6"
                    :style="{ animationDelay: `${i * 80}ms` }"
                >
                    <div class="absolute inset-x-0 top-0 h-1 origin-left scale-x-0 bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 transition duration-500 group-hover:scale-x-100" aria-hidden="true" />
                    <div class="flex items-center gap-4">
                        <ShopLogo :name="shop.name" :url="shop.logo_url" class="transition duration-300 group-hover:scale-110 group-hover:-rotate-3" />
                        <div class="min-w-0">
                            <h3 class="truncate text-lg font-bold text-gray-900 group-hover:text-indigo-700">{{ shop.name }}</h3>
                            <p class="text-sm text-gray-500">{{ shop.products_count }} {{ shop.products_count === 1 ? 'product' : 'products' }}</p>
                        </div>
                    </div>
                    <p v-if="shop.description" class="mt-4 line-clamp-3 text-sm text-gray-600">{{ shop.description }}</p>
                    <span class="mt-auto inline-flex items-center gap-1 pt-5 text-sm font-semibold text-indigo-600">
                        Visit shop <span class="transition group-hover:translate-x-1" aria-hidden="true">→</span>
                    </span>
                </Link>
            </div>
            <div v-else class="card">
                <EmptyState title="No shops are open yet" message="Please check back soon." />
            </div>
        </section>

        <!-- Latest products -->
        <section v-if="latest.length" class="mt-16">
            <p class="text-sm font-semibold uppercase tracking-widest text-indigo-600">Just added</p>
            <h2 class="mt-1 text-3xl font-extrabold tracking-tight text-gray-900">New in the <span class="text-gradient">shops</span></h2>

            <div class="mt-6 grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 lg:grid-cols-4">
                <ListingCard
                    v-for="(listing, i) in latest"
                    :key="`${listing.shop.slug}-${listing.slug}`"
                    :listing="listing"
                    :shop-slug="listing.shop.slug"
                    :index="i"
                    show-shop
                />
            </div>
        </section>
    </PublicLayout>
</template>
