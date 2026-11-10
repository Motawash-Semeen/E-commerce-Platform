<script setup>
// Price field with a "Tk" prefix.
const model = defineModel({ type: [String, Number], default: '' });

defineProps({
    id: { type: String, required: true },
    required: { type: Boolean, default: false },
});
</script>

<template>
    <div class="relative mt-1">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm font-medium text-gray-400">Tk</span>
        <input
            :id="id"
            v-model="model"
            type="number"
            min="0"
            step="0.01"
            :required="required"
            class="block w-full rounded-lg border-gray-300 pl-10 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        />
    </div>
</template>
