<script setup>
import { onBeforeUnmount, ref, watch } from 'vue';

/**
 * Image picker with a preview and drag-and-drop. v-model holds the chosen File (or null).
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
const dragging = ref(false);

function pick(file) {
    if (file && file.type.startsWith('image/')) model.value = file;
}

function onDrop(event) {
    dragging.value = false;
    pick(event.dataTransfer.files[0]);
}

// A temporary local URL for the selected file; the previous one is freed.
watch(model, (file) => {
    if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = file ? URL.createObjectURL(file) : null;
    if (!file && input.value) input.value.value = '';
});

onBeforeUnmount(() => previewUrl.value && URL.revokeObjectURL(previewUrl.value));
</script>

<template>
    <div class="mt-1 flex flex-col gap-4 sm:flex-row sm:items-center">
        <div
            class="relative flex h-28 w-28 shrink-0 items-center justify-center overflow-hidden border border-gray-200 bg-gray-50 shadow-inner"
            :class="props.shape === 'circle' ? 'rounded-full' : 'rounded-2xl'"
        >
            <img v-if="previewUrl || currentUrl" :key="previewUrl || currentUrl" :src="previewUrl || currentUrl" alt="Preview" class="h-full w-full animate-fade-in object-cover" />
            <svg v-else class="h-9 w-9 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0 0 21.75 19.5V4.5a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 4.5v15a1.5 1.5 0 0 0 1.5 1.5Z" />
            </svg>
            <span v-if="previewUrl" class="badge absolute bottom-1.5 left-1/2 -translate-x-1/2 bg-indigo-600 text-white shadow">New</span>
        </div>

        <label
            :for="id"
            class="flex flex-1 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed px-6 py-6 text-center transition"
            :class="dragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-indigo-400 hover:bg-indigo-50/40'"
            @dragover.prevent="dragging = true"
            @dragleave.prevent="dragging = false"
            @drop.prevent="onDrop"
        >
            <svg class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
            </svg>
            <span class="mt-2 text-sm font-semibold text-indigo-600">{{ model ? model.name : 'Click to upload' }}</span>
            <span class="text-xs text-gray-500">{{ model ? 'Click to choose another' : 'or drag and drop. JPG, PNG or WEBP, up to 2 MB' }}</span>
            <input :id="id" ref="input" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="pick($event.target.files[0])" />
        </label>

        <button v-if="model" type="button" class="self-start text-xs font-medium text-gray-500 underline hover:text-gray-700 sm:self-center" @click="model = null">
            Undo
        </button>
    </div>
</template>
