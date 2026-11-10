<script setup>
import PanelLayout from '@/Layouts/PanelLayout.vue';

// The log viewer is a package page, so it needs a full page load (inertia: false).
const links = [
    { label: 'Dashboard', href: route('admin.dashboard'), active: route().current('admin.dashboard') },
    { label: 'Shops', href: route('admin.shops.index'), active: route().current('admin.shops.*') },
    { label: 'Products', href: route('admin.products.index'), active: route().current('admin.products.*') },
    { label: 'Shop owners', href: route('admin.shop-owners.index'), active: route().current('admin.shop-owners.*') },
    { label: 'Categories', href: route('admin.categories.index'), active: route().current('admin.categories.*') },
    { label: 'Subcategories', href: route('admin.subcategories.index'), active: route().current('admin.subcategories.*') },
    { label: 'Logs', href: route('log-viewer.index'), active: false, inertia: false },
];
</script>

<template>
    <PanelLayout :links="links">
        <template v-if="$slots.header" #header><slot name="header" /></template>
        <slot />
    </PanelLayout>
</template>
