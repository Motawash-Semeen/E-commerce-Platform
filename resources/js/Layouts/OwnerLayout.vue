<script setup>
import { computed } from 'vue';
import AppNav from '@/Components/AppNav.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

// Owner links grow in later steps (catalog, my products, listings).
const links = computed(() => [
    { label: 'Dashboard', href: route('owner.dashboard'), active: route().current('owner.dashboard') },
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
