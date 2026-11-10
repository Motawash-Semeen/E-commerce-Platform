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
        <Link :href="route('home')" class="text-sm font-medium text-gray-500 hover:text-indigo-600">← All shops</Link>

        <!-- Shop header -->
        <section class="card mt-4 overflow-hidden">
            <div class="h-24 bg-gradient-to-r from-indigo-500 via-indigo-400 to-purple-500 sm:h-32" />
            <div class="flex flex-col gap-4 px-6 pb-6 sm:flex-row sm:items-end">
                <ShopLogo :name="shop.name" :url="shop.logo_url" size="-mt-10 h-20 w-20 text-3xl ring-4 ring-white sm:-mt-12 sm:h-24 sm:w-24" />
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
                class="rounded-full border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-600 shadow-sm hover:border-indigo-200 hover:text-indigo-700"
            >{{ section.subcategory }}</a>
        </nav>

        <!-- Products grouped by Category › Subcategory -->
        <section v-for="section in sections" :id="sectionId(section)" :key="sectionId(section)" class="mt-10 scroll-mt-24">
            <h2 class="text-xl font-bold tracking-tight text-gray-900">
                <span class="text-gray-400">{{ section.category }} ›</span> {{ section.subcategory }}
            </h2>
            <div class="mt-4 grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 lg:grid-cols-4">
                <ListingCard v-for="item in section.items" :key="item.slug" :listing="item" :shop-slug="shop.slug" />
            </div>
        </section>

        <div v-if="!sections.length" class="card mt-8">
            <EmptyState title="No products here yet" message="This shop hasn't listed any products. Check back soon." />
        </div>
    </PublicLayout>
</template>
