<script setup>
// On/off switch with a label and help text: <ToggleSwitch v-model="form.is_active" label="Active" />
const model = defineModel({ type: Boolean, default: false });

defineProps({
    label: { type: String, required: true },
    description: { type: String, default: '' },
});
</script>

<template>
    <div class="flex items-center justify-between gap-6">
        <div>
            <p class="text-sm font-medium text-gray-900">{{ label }}</p>
            <p v-if="description" class="text-sm text-gray-500">{{ description }}</p>
        </div>
        <button
            type="button"
            role="switch"
            :aria-checked="model"
            :aria-label="label"
            class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            :class="model ? 'bg-gradient-to-r from-indigo-600 to-violet-600' : 'bg-gray-200'"
            @click="model = !model"
        >
            <span
                class="pointer-events-none mt-1 inline-block h-5 w-5 rounded-full bg-white shadow-md transition-transform duration-300"
                :class="model ? 'translate-x-6' : 'translate-x-1'"
                aria-hidden="true"
            />
        </button>
    </div>
</template>
