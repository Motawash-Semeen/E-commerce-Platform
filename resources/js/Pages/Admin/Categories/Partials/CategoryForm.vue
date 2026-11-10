<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

// Shared by Create and Edit. Without a category it creates, with one it updates.
const props = defineProps({
    category: { type: Object, default: null },
});

const form = useForm({
    name: props.category?.name ?? '',
});

function submit() {
    if (props.category) {
        form.put(route('admin.categories.update', props.category.slug));
    } else {
        form.post(route('admin.categories.store'));
    }
}
</script>

<template>
    <form class="card max-w-xl space-y-6 p-6" @submit.prevent="submit">
        <div>
            <label for="name" class="form-label">Name</label>
            <input id="name" v-model="form.name" type="text" maxlength="100" class="form-input" required autofocus />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary" :disabled="form.processing">
                {{ category ? 'Save changes' : 'Create category' }}
            </button>
            <Link :href="route('admin.categories.index')" class="btn-secondary">Cancel</Link>
        </div>
    </form>
</template>
