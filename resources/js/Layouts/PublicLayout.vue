<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppNav from '@/Components/AppNav.vue';
import BrandMark from '@/Components/BrandMark.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

// Customer-facing layout (guests and logged-in users alike).
const page = usePage();
const links = computed(() => {
    const items = [{ label: 'All shops', href: route('home'), active: route().current('home') }];

    if (page.props.auth.user) {
        items.push({ label: 'My dashboard', href: route('dashboard'), active: false });
    }

    return items;
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-gray-50">
        <AppNav :links="links" />

        <main class="mx-auto w-full max-w-7xl flex-1 animate-fade-up px-4 py-8 sm:px-6 lg:px-8">
            <FlashMessage />
            <slot />
        </main>

        <footer class="relative mt-16 overflow-hidden bg-gray-950 text-sm text-gray-400">
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500 to-transparent" aria-hidden="true" />
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-8 sm:flex-row sm:px-6 lg:px-8">
                <Link :href="route('home')" class="[&_span]:text-white">
                    <BrandMark />
                </Link>
                <p>&copy; {{ new Date().getFullYear() }} All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
