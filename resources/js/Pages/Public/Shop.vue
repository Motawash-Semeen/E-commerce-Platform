<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import ListingCard from '@/Components/ListingCard.vue';
import ShopLogo from '@/Components/ShopLogo.vue';

defineProps({
    shop: { type: Object, required: true },      // { name, slug, description, logo_url }
    sections: { type: Array, required: true },   // [{ category, subcategory, items: [listing card] }]
    productsCount: { type: Number, required: true },
});

const sectionId = (section) => `${section.category}-${section.subcategory}`.toLowerCase().replace(/[^a-z0-9]+/g, '-');
</script>

<template>
    <Head :title="shop.name" />

    <PublicLayout>
        <Link :href="route('home')" class="group inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-indigo-600">
            <span class="transition group-hover:-translate-x-1" aria-hidden="true">←</span> All shops
        </Link>

        <!-- Shop header -->
        <section class="card mt-4 overflow-hidden">
            <div class="relative h-28 overflow-hidden bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-500 bg-[length:200%_200%] animate-gradient sm:h-40">
                <div class="absolute -right-10 -top-16 h-56 w-56 rounded-full bg-white/15 blur-2xl animate-blob" aria-hidden="true" />
                <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.15)_1px,transparent_1px)] [background-size:20px_20px]" aria-hidden="true" />
            </div>
            <div class="flex flex-col gap-4 px-6 pb-6 sm:flex-row sm:items-end">
                <ShopLogo :name="shop.name" :url="shop.logo_url" size="relative -mt-10 h-20 w-20 text-3xl shadow-xl ring-4 ring-white sm:-mt-12 sm:h-24 sm:w-24" />
                <div class="min-w-0 flex-1">
                    <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">{{ shop.name }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ productsCount }} {{ productsCount === 1 ? 'product' : 'products' }}</p>
                </div>
            </div>
            <p v-if="shop.description" class="whitespace-pre-line border-t border-gray-100 px-6 py-5 text-gray-600">{{ shop.description }}</p>
        </section>

        <!-- Jump links -->
        <nav v-if="sections.length > 1" class="mt-8 flex flex-wrap gap-2" aria-label="Sections">
            <a
                v-for="section in sections"
                :key="sectionId(section)"
                :href="`#${sectionId(section)}`"
                class="rounded-full border border-gray-200 bg-white px-4 py-1.5 text-sm font-medium text-gray-600 shadow-sm transition hover:-translate-y-0.5 hover:border-transparent hover:bg-gradient-to-r hover:from-indigo-600 hover:to-violet-600 hover:text-white hover:shadow-md"
            >{{ section.subcategory }}</a>
        </nav>

        <!-- Products grouped by Category › Subcategory -->
        <section v-for="section in sections" :id="sectionId(section)" :key="sectionId(section)" class="mt-10 scroll-mt-24">
            <h2 class="flex items-center gap-3 text-xl font-bold tracking-tight text-gray-900">
                <span class="h-6 w-1.5 rounded-full bg-gradient-to-b from-indigo-500 to-fuchsia-500" aria-hidden="true" />
                <span><span class="text-gray-400">{{ section.category }} ›</span> {{ section.subcategory }}</span>
            </h2>
            <div class="mt-4 grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 lg:grid-cols-4">
                <ListingCard v-for="(item, i) in section.items" :key="item.slug" :listing="item" :shop-slug="shop.slug" :index="i" />
            </div>
        </section>

        <div v-if="!sections.length" class="card mt-8">
            <EmptyState title="No products here yet" message="This shop hasn't listed any products. Check back soon." />
        </div>
    </PublicLayout>
</template>
