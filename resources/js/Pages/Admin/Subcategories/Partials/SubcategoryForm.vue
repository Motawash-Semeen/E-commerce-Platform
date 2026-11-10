<script setup>
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Components/FormActions.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';

// Shared by Create and Edit. Without a subcategory it creates, with one it updates.
const props = defineProps({
    subcategory: { type: Object, default: null },
    categories: { type: Array, required: true },  // [{ id, name }]
});

const form = useForm({
    category_id: props.subcategory?.category_id ?? '',
    name: props.subcategory?.name ?? '',
});

function submit() {
    if (props.subcategory) {
        form.put(route('admin.subcategories.update', props.subcategory.slug));
    } else {
        form.post(route('admin.subcategories.store'));
    }
}
</script>

<template>
    <form @submit.prevent="submit">
        <FormSection title="Subcategory details" description="A subcategory sits inside one category. Names only need to be unique within their category.">
            <div>
                <label for="category_id" class="form-label">Category</label>
                <select id="category_id" v-model="form.category_id" class="form-input" required>
                    <option value="" disabled>Choose a category</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.category_id" />
            </div>

            <div>
                <label for="name" class="form-label">Name</label>
                <input id="name" v-model="form.name" type="text" maxlength="100" class="form-input" placeholder="e.g. Headphones" required />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
        </FormSection>

        <FormActions
            :cancel-href="route('admin.subcategories.index')"
            :processing="form.processing"
            :dirty="form.isDirty"
            :submit-label="subcategory ? 'Save changes' : 'Create subcategory'"
        />
    </form>
</template>
