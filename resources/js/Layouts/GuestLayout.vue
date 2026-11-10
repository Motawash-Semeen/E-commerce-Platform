<script setup>
import { Link } from '@inertiajs/vue3';
import BrandMark from '@/Components/BrandMark.vue';

// Centered card on an animated gradient, used by the login and password-reset pages.
</script>

<template>
    <div class="relative flex min-h-screen flex-col items-center overflow-hidden bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-500 bg-[length:200%_200%] px-4 pt-10 animate-gradient sm:justify-center sm:pt-0">
        <div class="absolute -left-24 top-10 h-80 w-80 rounded-full bg-white/10 blur-3xl animate-blob" aria-hidden="true" />
        <div class="absolute -bottom-24 -right-16 h-96 w-96 rounded-full bg-fuchsia-300/25 blur-3xl animate-blob [animation-delay:-5s]" aria-hidden="true" />
        <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.12)_1px,transparent_1px)] [background-size:22px_22px]" aria-hidden="true" />

        <Link :href="route('home')" class="group relative animate-fade-up rounded-2xl bg-white/90 px-4 py-2 shadow-lg backdrop-blur">
            <BrandMark />
        </Link>

        <div class="relative mt-6 w-full animate-fade-up overflow-hidden rounded-2xl bg-white px-6 py-8 shadow-2xl shadow-indigo-900/30 [animation-delay:120ms] sm:max-w-md">
            <slot />
        </div>
    </div>
</template>
