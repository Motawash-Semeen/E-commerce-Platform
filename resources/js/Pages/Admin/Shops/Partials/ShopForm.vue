<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import ImagePreview from '@/Components/ImagePreview.vue';
import InputError from '@/Components/InputError.vue';

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
    <form class="card max-w-2xl space-y-6 p-6" @submit.prevent="submit">
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
            <textarea id="description" v-model="form.description" rows="4" maxlength="2000" class="form-input" />
            <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <div>
            <label for="logo" class="form-label">Logo <span class="font-normal text-gray-400">(optional)</span></label>
            <ImagePreview id="logo" v-model="form.logo" :current-url="shop?.logo_url" shape="circle" />
            <InputError class="mt-2" :message="form.errors.logo" />
        </div>

        <label class="flex items-start gap-3 rounded-xl border border-gray-200 p-4">
            <input v-model="form.is_active" type="checkbox" class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
            <span>
                <span class="block text-sm font-medium text-gray-900">Active</span>
                <span class="block text-xs text-gray-500">Inactive shops and their products are hidden from customers.</span>
            </span>
        </label>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary" :disabled="form.processing">
                {{ shop ? 'Save changes' : 'Create shop' }}
            </button>
            <Link :href="route('admin.shops.index')" class="btn-secondary">Cancel</Link>
        </div>
    </form>
</template>
