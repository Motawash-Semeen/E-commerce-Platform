<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

/**
 * Trash button that asks for confirmation in a small dialog, then sends DELETE to `url`.
 */
const props = defineProps({
    url: { type: String, required: true },
    title: { type: String, required: true },        // e.g. 'Delete "Fashion"?'
    message: { type: String, default: 'This cannot be undone.' },
    label: { type: String, default: 'Delete' },
});

const open = ref(false);
const deleting = ref(false);

function confirmDelete() {
    deleting.value = true;
    router.delete(props.url, {
        preserveScroll: true,
        onFinish: () => {
            deleting.value = false;
            open.value = false;
        },
    });
}
</script>

<template>
    <button
        type="button"
        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
        :title="label"
        @click="open = true"
    >
        <span class="sr-only">{{ label }}</span>
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
        </svg>
    </button>

    <Teleport to="body">
        <Transition enter-from-class="opacity-0" enter-active-class="transition duration-200" leave-to-class="opacity-0" leave-active-class="transition duration-150">
            <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4 backdrop-blur-sm" @click.self="open = false" @keydown.esc="open = false">
                <div class="w-full max-w-md animate-fade-up rounded-2xl bg-white p-6 text-left shadow-2xl" role="dialog" aria-modal="true">
                    <div class="flex gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
                            <p class="mt-1 whitespace-normal text-sm text-gray-500">{{ message }}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" class="btn-secondary" @click="open = false">Cancel</button>
                        <button type="button" class="btn-danger" :disabled="deleting" autofocus @click="confirmDelete">
                            {{ deleting ? 'Deleting...' : label }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
