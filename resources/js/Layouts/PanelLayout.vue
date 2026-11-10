<script setup>
import AppNav from '@/Components/AppNav.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

// Shared frame of the admin and owner areas; AdminLayout / OwnerLayout only pass their links.
defineProps({
    links: { type: Array, required: true },
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppNav :links="links" />

        <header v-if="$slots.header" class="relative overflow-hidden border-b border-gray-200 bg-gradient-to-br from-white via-indigo-50/60 to-violet-50/60">
            <div class="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full bg-violet-200/40 blur-3xl" aria-hidden="true" />
            <div class="relative mx-auto max-w-7xl animate-fade-in px-4 py-7 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main class="mx-auto max-w-7xl animate-fade-up px-4 py-8 sm:px-6 lg:px-8">
            <FlashMessage />
            <slot />
        </main>
    </div>
</template>
