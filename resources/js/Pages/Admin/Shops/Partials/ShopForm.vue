<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import FormActions from '@/Components/FormActions.vue';
import FormSection from '@/Components/FormSection.vue';
import ImagePreview from '@/Components/ImagePreview.vue';
import InputError from '@/Components/InputError.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';

// Shared by Create and Edit.
const props = defineProps({
    shop: { type: Object, default: null },       // { id, name, slug, description, owner_id, is_active, logo_url }
    owners: { type: Array, required: true },     // [{ id, name, email }] owners without a shop (+ the current one)
});

const form = useForm({
    name: props.shop?.name ?? '',
    owner_id: props.shop?.owner_id ?? '',
    description: props.shop?.description ?? '',
    logo: null,
    is_active: props.shop?.is_active ?? true,
});

function submit() {
    if (props.shop) {
        // Files can't be sent with a real PUT, so POST with _method=put.
        form.transform((data) => ({ ...data, _method: 'put' }))
            .post(route('admin.shops.update', props.shop.slug), { forceFormData: true });
    } else {
        form.post(route('admin.shops.store'), { forceFormData: true });
    }
}
</script>

<template>
    <form @submit.prevent="submit">
        <FormSection title="Shop details" description="Customers see the name and description on the shop page.">
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="form-label">Shop name</label>
                    <input id="name" v-model="form.name" type="text" maxlength="150" class="form-input" required autofocus />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div>
                    <label for="owner_id" class="form-label">Owner</label>
                    <select id="owner_id" v-model="form.owner_id" class="form-input" required>
                        <option value="" disabled>Choose a shop owner</option>
                        <option v-for="owner in owners" :key="owner.id" :value="owner.id">{{ owner.name }} ({{ owner.email }})</option>
                    </select>
                    <p v-if="!owners.length" class="form-hint">
                        Every shop owner already has a shop.
                        <Link :href="route('admin.shop-owners.create')" class="font-medium text-indigo-600 hover:underline">Create a shop owner</Link>
                    </p>
                    <InputError class="mt-2" :message="form.errors.owner_id" />
                </div>
            </div>

            <div>
                <label for="description" class="form-label">Description <span class="font-normal text-gray-400">(optional)</span></label>
                <textarea id="description" v-model="form.description" rows="4" maxlength="2000" class="form-input" placeholder="What does this shop sell?" />
                <InputError class="mt-2" :message="form.errors.description" />
            </div>
        </FormSection>

        <FormSection title="Logo" description="Optional. Without a logo the shop shows its first letter.">
            <ImagePreview id="logo" v-model="form.logo" :current-url="shop?.logo_url" shape="circle" />
            <InputError :message="form.errors.logo" />
        </FormSection>

        <FormSection title="Visibility" description="Inactive shops and all their products are hidden from customers.">
            <ToggleSwitch v-model="form.is_active" label="Shop is open" description="Customers can see this shop and its products." />
        </FormSection>

        <FormActions
            :cancel-href="route('admin.shops.index')"
            :processing="form.processing"
            :dirty="form.isDirty"
            :submit-label="shop ? 'Save changes' : 'Create shop'"
        />
    </form>
</template>
