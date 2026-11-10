<script setup>
import { Link } from '@inertiajs/vue3';

// Save / Cancel bar that stays visible at the bottom of long forms.
defineProps({
    cancelHref: { type: String, required: true },
    processing: { type: Boolean, default: false },
    dirty: { type: Boolean, default: false },
    submitLabel: { type: String, default: 'Save' },
});
</script>

<template>
    <div class="sticky bottom-4 z-10 mt-2 flex items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-white/90 px-4 py-3 shadow-lg shadow-gray-900/5 backdrop-blur">
        <p class="text-sm text-gray-500">
            <span v-if="dirty" class="inline-flex items-center gap-2 text-amber-600">
                <span class="h-2 w-2 animate-pulse rounded-full bg-amber-500" aria-hidden="true" /> Unsaved changes
            </span>
        </p>
        <div class="flex items-center gap-3">
            <Link :href="cancelHref" class="btn-secondary">Cancel</Link>
            <button type="submit" class="btn-primary min-w-28" :disabled="processing">
                <svg v-if="processing" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z" />
                </svg>
                {{ processing ? 'Saving...' : submitLabel }}
            </button>
        </div>
    </div>
</template>
