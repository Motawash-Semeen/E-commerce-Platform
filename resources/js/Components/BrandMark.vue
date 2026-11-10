<script setup>
// The app's logo: a rounded indigo square with a shopping bag, plus the app name.
defineProps({
    showName: { type: Boolean, default: true },
});

const appName = import.meta.env.VITE_APP_NAME || 'Shop';
</script>

<template>
    <span class="inline-flex items-center gap-2">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-md shadow-indigo-500/30 transition duration-300 group-hover:-rotate-6 group-hover:scale-110">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
            </svg>
        </span>
        <span v-if="showName" class="text-lg font-bold tracking-tight text-gray-900">{{ appName }}</span>
    </span>
</template>
