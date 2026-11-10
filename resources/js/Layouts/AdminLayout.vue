<script setup>
import { computed } from 'vue';
import AppNav from '@/Components/AppNav.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

// Every admin page is a Vue page; only the log viewer (a package UI) needs a full page load.
const links = computed(() => [
    { label: 'Dashboard', href: route('admin.dashboard'), active: route().current('admin.dashboard') },
    { label: 'Shops', href: route('admin.shops.index'), active: route().current('admin.shops.*') },
    { label: 'Products', href: route('admin.products.index'), active: route().current('admin.products.*') },
    { label: 'Shop owners', href: route('admin.shop-owners.index'), active: route().current('admin.shop-owners.*') },
    { label: 'Categories', href: route('admin.categories.index'), active: route().current('admin.categories.*') },
    { label: 'Subcategories', href: route('admin.subcategories.index'), active: route().current('admin.subcategories.*') },
    { label: 'Logs', href: route('log-viewer.index'), active: false, inertia: false },
]);
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppNav :links="links" />

        <header v-if="$slots.header" class="border-b border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <FlashMessage />
            <slot />
        </main>
    </div>
</template>
