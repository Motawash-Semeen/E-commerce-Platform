<script setup>
// One block of a form: title and help text on the left, the fields in a card on the right.
defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
});
</script>

<template>
    <section class="grid gap-4 border-b border-gray-200/70 py-8 first-of-type:pt-0 last-of-type:border-0 lg:grid-cols-3 lg:gap-8">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ title }}</h2>
            <p v-if="description" class="mt-1 text-sm leading-6 text-gray-500">{{ description }}</p>
        </div>
        <div class="card space-y-6 p-6 lg:col-span-2">
            <slot />
        </div>
    </section>
</template>
