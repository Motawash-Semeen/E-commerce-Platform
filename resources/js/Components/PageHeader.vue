<script setup>
// Title row for the layout's #header slot: title, optional subtitle, actions on the right.
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h1 class="truncate text-2xl font-bold tracking-tight text-gray-900">{{ title }}</h1>
            <p v-if="subtitle" class="mt-1 text-sm text-gray-500">{{ subtitle }}</p>
        </div>
        <div v-if="$slots.default" class="flex shrink-0 flex-wrap items-center gap-2">
            <slot />
        </div>
    </div>
</template>
