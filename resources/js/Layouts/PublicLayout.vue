<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppNav from '@/Components/AppNav.vue';
import BrandMark from '@/Components/BrandMark.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

// Customer-facing layout. The shop pages become Vue in Step 9; until then
// "Shops" points at the current home page with a normal page load.
const page = usePage();
const links = computed(() => {
    const items = [{ label: 'Shops', href: '/', active: false, inertia: false }];

    if (page.props.auth.user) {
        items.push({ label: 'My dashboard', href: route('dashboard'), active: false });
    }

    return items;
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-gray-50">
        <AppNav :links="links" />

        <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
            <FlashMessage />
            <slot />
        </main>

        <footer class="bg-gray-900 text-sm text-gray-400">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 py-6 sm:flex-row sm:px-6 lg:px-8">
                <BrandMark :show-name="false" />
                <p>&copy; {{ new Date().getFullYear() }} All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
