<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Shows the one-time `flash.success` / `flash.error` shared by HandleInertiaRequests.
const page = usePage();
const flash = computed(() => page.props.flash ?? {});
const hidden = ref(false);

// A new message (after the next visit) should show again even if the last one was closed.
watch(flash, () => (hidden.value = false), { deep: true });
</script>

<template>
    <div v-if="!hidden && (flash.success || flash.error)" class="mb-6">
        <div
            v-if="flash.success"
            role="status"
            class="flex items-start gap-3 rounded-xl border-l-4 border-green-500 bg-green-50 p-4 text-green-800 shadow-sm"
        >
            <span aria-hidden="true">✓</span>
            <p class="flex-1 text-sm font-medium">{{ flash.success }}</p>
            <button type="button" class="text-green-700 hover:text-green-900" aria-label="Close" @click="hidden = true">✕</button>
        </div>
        <div
            v-if="flash.error"
            role="alert"
            class="flex items-start gap-3 rounded-xl border-l-4 border-red-500 bg-red-50 p-4 text-red-800 shadow-sm"
            :class="{ 'mt-3': flash.success }"
        >
            <span aria-hidden="true">!</span>
            <p class="flex-1 text-sm font-medium">{{ flash.error }}</p>
            <button type="button" class="text-red-700 hover:text-red-900" aria-label="Close" @click="hidden = true">✕</button>
        </div>
    </div>
</template>
