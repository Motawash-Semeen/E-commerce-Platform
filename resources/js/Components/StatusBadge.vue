<script setup>
// Green "Active" / grey "Inactive" pill.
defineProps({
    active: { type: Boolean, required: true },
    on: { type: String, default: 'Active' },
    off: { type: String, default: 'Inactive' },
});
</script>

<template>
    <span class="badge" :class="active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'">
        <span class="mr-1.5 h-1.5 w-1.5 rounded-full" :class="active ? 'bg-green-500' : 'bg-gray-400'" aria-hidden="true" />
        {{ active ? on : off }}
    </span>
</template>
