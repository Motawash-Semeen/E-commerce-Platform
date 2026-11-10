<script setup>
import PanelLayout from '@/Layouts/PanelLayout.vue';

const links = [
    { label: 'Dashboard', href: route('owner.dashboard'), active: route().current('owner.dashboard') },
    { label: 'Browse catalog', href: route('owner.catalog.index'), active: route().current('owner.catalog.*') },
    { label: 'My listings', href: route('owner.listings.index'), active: route().current('owner.listings.*') },
    { label: 'My products', href: route('owner.products.index'), active: route().current('owner.products.*') },
];
</script>

<template>
    <PanelLayout :links="links">
        <template v-if="$slots.header" #header><slot name="header" /></template>
        <slot />
    </PanelLayout>
</template>
