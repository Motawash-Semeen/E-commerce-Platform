<script setup>
import { Link } from '@inertiajs/vue3';

// Title row for the layout's #header slot: optional back link and count, actions on the right.
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    backHref: { type: String, default: null },
    backLabel: { type: String, default: 'Back' },
    count: { type: Number, default: null },
});
</script>

<template>
    <div>
        <Link v-if="backHref" :href="backHref" class="group mb-3 inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-indigo-600">
            <span class="transition group-hover:-translate-x-1" aria-hidden="true">←</span> {{ backLabel }}
        </Link>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h1 class="flex flex-wrap items-center gap-x-3 gap-y-1 break-words text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                    {{ title }}
                    <span v-if="count !== null" class="badge bg-indigo-100 text-sm text-indigo-700">{{ count }}</span>
                </h1>
                <p v-if="subtitle" class="mt-1 text-sm text-gray-500">{{ subtitle }}</p>
            </div>
            <div v-if="$slots.default" class="flex shrink-0 flex-wrap items-center gap-2">
                <slot />
            </div>
        </div>
    </div>
</template>
