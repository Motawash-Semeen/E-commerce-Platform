<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

/**
 * Search box that keeps the term in the query string (?search=...).
 * Waits 300 ms after typing, then reloads the page's props with preserveState.
 */
const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Search...' },
    extra: { type: Object, default: () => ({}) }, // other filters to keep, e.g. { category: 3 }
});

const term = ref(props.modelValue ?? '');
let timer = null;

watch(term, (value) => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            window.location.pathname,
            { ...props.extra, search: value || undefined },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }, 300);
});
</script>

<template>
    <div class="relative w-full sm:max-w-xs">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
        </svg>
        <input
            v-model="term"
            type="search"
            :placeholder="placeholder"
            aria-label="Search"
            class="block w-full rounded-lg border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        />
    </div>
</template>
