<script setup>
import { Link } from '@inertiajs/vue3';
import BrandMark from '@/Components/BrandMark.vue';

// Centered card used by the login and password-reset pages.
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-gray-50 px-4 pt-10 sm:justify-center sm:pt-0">
        <Link href="/">
            <BrandMark />
        </Link>

        <div class="mt-6 w-full overflow-hidden rounded-2xl border border-gray-200 bg-white px-6 py-8 shadow-sm sm:max-w-md">
            <slot />
        </div>
    </div>
</template>
