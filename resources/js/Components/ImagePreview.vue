<script setup>
import { onBeforeUnmount, ref, watch } from 'vue';

/**
 * File input with a live preview: v-model holds the chosen File (or null).
 * `currentUrl` is shown until a new file is picked (e.g. the existing image on edit).
 */
const model = defineModel({ default: null });

const props = defineProps({
    id: { type: String, required: true },
    currentUrl: { type: String, default: null },
    shape: { type: String, default: 'square' }, // 'square' | 'circle'
});

const input = ref(null);
const previewUrl = ref(null);

function onChange(event) {
    model.value = event.target.files[0] ?? null;
}

// Build a temporary local URL for the selected file, and free the previous one.
watch(model, (file) => {
    if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = file ? URL.createObjectURL(file) : null;
    if (!file && input.value) input.value.value = '';
});

onBeforeUnmount(() => {
    if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
});
</script>

<template>
    <div class="mt-1 flex items-center gap-4">
        <div
            class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden border border-gray-200 bg-gray-50"
            :class="props.shape === 'circle' ? 'rounded-full' : 'rounded-xl'"
        >
            <img v-if="previewUrl || currentUrl" :src="previewUrl || currentUrl" alt="Preview" class="h-full w-full object-cover" />
            <svg v-else class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0 0 21.75 19.5V4.5a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 4.5v15a1.5 1.5 0 0 0 1.5 1.5Z" />
            </svg>
        </div>

        <div class="min-w-0">
            <input
                :id="id"
                ref="input"
                type="file"
                accept="image/jpeg,image/png,image/webp"
                class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                @change="onChange"
            />
            <p class="form-hint">JPG, PNG or WEBP, up to 2 MB.</p>
            <button v-if="model" type="button" class="mt-1 text-xs font-medium text-gray-500 underline hover:text-gray-700" @click="model = null">
                Remove selected file
            </button>
        </div>
    </div>
</template>
