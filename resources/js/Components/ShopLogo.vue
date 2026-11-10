<script setup>
// The shop's logo, or its first letter on a gradient when it has none.
defineProps({
    name: { type: String, required: true },
    url: { type: String, default: null },
    size: { type: String, default: 'h-14 w-14 text-xl' },
});
</script>

<template>
    <img v-if="url" :src="url" :alt="`${name} logo`" class="shrink-0 rounded-2xl border border-gray-200 bg-white object-cover" :class="size" />
    <span
        v-else
        class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 font-bold text-white shadow-sm"
        :class="size"
        aria-hidden="true"
    >{{ name.charAt(0).toUpperCase() }}</span>
</template>
