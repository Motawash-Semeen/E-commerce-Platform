<script setup>
import { onMounted, ref } from 'vue';

// Counts up from 0 to `value` when the page opens (dashboard stats).
const props = defineProps({
    value: { type: Number, required: true },
    duration: { type: Number, default: 900 },
});

const shown = ref(0);

onMounted(() => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        shown.value = props.value;
        return;
    }

    const start = performance.now();
    const step = (now) => {
        const progress = Math.min((now - start) / props.duration, 1);
        shown.value = Math.round(props.value * (1 - Math.pow(1 - progress, 3)));   // ease-out
        if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
});
</script>

<template>
    <span>{{ shown }}</span>
</template>
