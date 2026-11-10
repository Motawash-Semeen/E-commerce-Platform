<script setup>
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Components/FormActions.vue';
import FormSection from '@/Components/FormSection.vue';
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
    <form @submit.prevent="submit">
        <FormSection title="Category details" description="The name customers see above a group of subcategories. The web address is made from it.">
            <div>
                <label for="name" class="form-label">Name</label>
                <input id="name" v-model="form.name" type="text" maxlength="100" class="form-input" placeholder="e.g. Electronics" required autofocus />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
        </FormSection>

        <FormActions
            :cancel-href="route('admin.categories.index')"
            :processing="form.processing"
            :dirty="form.isDirty"
            :submit-label="category ? 'Save changes' : 'Create category'"
        />
    </form>
</template>
