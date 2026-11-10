<script setup>
import { Link } from '@inertiajs/vue3';

/**
 * Renders a Laravel paginator passed as a prop: <Pagination :paginator="products" />.
 * Uses the paginator's own links, so search/filter query strings are kept
 * (controllers call ->withQueryString()).
 */
defineProps({
    paginator: { type: Object, required: true },
});

// Laravel's labels contain HTML entities ("&laquo; Previous"); swap them for plain
// characters so they can be printed safely with {{ }} (no v-html).
const label = (text) => text.replace('&laquo;', '‹').replace('&raquo;', '›');
</script>

<template>
    <div v-if="paginator.last_page > 1" class="flex flex-col items-center justify-between gap-3 sm:flex-row">
        <p class="text-sm text-gray-500">
            Showing <span class="font-medium text-gray-700">{{ paginator.from }}</span>
            to <span class="font-medium text-gray-700">{{ paginator.to }}</span>
            of <span class="font-medium text-gray-700">{{ paginator.total }}</span>
        </p>

        <nav class="flex flex-wrap gap-1" aria-label="Pagination">
            <template v-for="(link, index) in paginator.links" :key="index">
                <span v-if="!link.url" class="rounded-lg px-3 py-1.5 text-sm text-gray-400">{{ label(link.label) }}</span>
                <Link
                    v-else
                    :href="link.url"
                    preserve-scroll
                    class="rounded-lg px-3 py-1.5 text-sm font-medium transition"
                    :class="link.active ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                    :aria-current="link.active ? 'page' : undefined"
                >{{ label(link.label) }}</Link>
            </template>
        </nav>
    </div>
</template>
