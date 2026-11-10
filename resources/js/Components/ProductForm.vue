<script setup>
import { computed, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import ImagePreview from '@/Components/ImagePreview.vue';
import InputError from '@/Components/InputError.vue';

/**
 * Catalog product form shared by the admin and shop owners, for create and edit.
 * Without `product` it POSTs to storeUrl; with one it updates via updateUrl.
 */
const props = defineProps({
    product: { type: Object, default: null },      // { category_id, subcategory_id, name, description, old_price, new_price, image_url }
    categories: { type: Array, required: true },   // [{ id, name, subcategories: [{ id, name }] }]
    storeUrl: { type: String, default: null },
    updateUrl: { type: String, default: null },
    cancelUrl: { type: String, required: true },
    canAddToShop: { type: Boolean, default: false }, // owner create page with a shop: show "Also add to my shop"
});

const form = useForm({
    category_id: props.product?.category_id ?? '',
    subcategory_id: props.product?.subcategory_id ?? '',
    name: props.product?.name ?? '',
    description: props.product?.description ?? '',
    old_price: props.product?.old_price ?? '',
    new_price: props.product?.new_price ?? '',
    image: null,
    add_to_shop: props.canAddToShop,
});

// Dependent dropdown: only the chosen category's subcategories (no extra request).
const subcategories = computed(
    () => props.categories.find((category) => category.id === Number(form.category_id))?.subcategories ?? [],
);

// A new category means the old subcategory no longer fits. (On edit, the
// initial values are set before this watcher exists, so they are kept.)
watch(() => form.category_id, () => {
    form.subcategory_id = '';
});

function submit() {
    if (props.product) {
        // Files can't be sent with a real PUT, so POST with _method=put.
        form.transform((data) => ({ ...data, _method: 'put' }))
            .post(props.updateUrl, { forceFormData: true });
    } else {
        form.post(props.storeUrl, { forceFormData: true });
    }
}
</script>

<template>
    <form class="card max-w-3xl space-y-6 p-6" @submit.prevent="submit">
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label for="category_id" class="form-label">Category</label>
                <select id="category_id" v-model="form.category_id" class="form-input" required>
                    <option value="" disabled>Choose a category</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.category_id" />
            </div>

            <div>
                <label for="subcategory_id" class="form-label">Subcategory</label>
                <select id="subcategory_id" v-model="form.subcategory_id" class="form-input" :disabled="!subcategories.length" required>
                    <option value="" disabled>{{ form.category_id ? 'Choose a subcategory' : 'Choose a category first' }}</option>
                    <option v-for="subcategory in subcategories" :key="subcategory.id" :value="subcategory.id">{{ subcategory.name }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.subcategory_id" />
            </div>
        </div>

        <div>
            <label for="name" class="form-label">Product name</label>
            <input id="name" v-model="form.name" type="text" maxlength="150" class="form-input" required />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
            <label for="description" class="form-label">Description</label>
            <textarea id="description" v-model="form.description" rows="5" maxlength="5000" class="form-input" required />
            <p class="form-hint">Line breaks are kept.</p>
            <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label for="new_price" class="form-label">Suggested price (Tk)</label>
                <input id="new_price" v-model="form.new_price" type="number" min="0" step="0.01" class="form-input" required />
                <InputError class="mt-2" :message="form.errors.new_price" />
            </div>
            <div>
                <label for="old_price" class="form-label">Old price (Tk) <span class="font-normal text-gray-400">(optional)</span></label>
                <input id="old_price" v-model="form.old_price" type="number" min="0" step="0.01" class="form-input" />
                <p class="form-hint">Shown struck through when higher than the price.</p>
                <InputError class="mt-2" :message="form.errors.old_price" />
            </div>
        </div>

        <div>
            <label for="image" class="form-label">Image <span v-if="product" class="font-normal text-gray-400">(leave empty to keep the current one)</span></label>
            <ImagePreview id="image" v-model="form.image" :current-url="product?.image_url" />
            <InputError class="mt-2" :message="form.errors.image" />
        </div>

        <label v-if="canAddToShop" class="flex items-start gap-3 rounded-xl border border-indigo-100 bg-indigo-50/50 p-4">
            <input v-model="form.add_to_shop" type="checkbox" class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
            <span>
                <span class="block text-sm font-medium text-gray-900">Also add to my shop</span>
                <span class="block text-xs text-gray-500">Lists it in your shop right away at the suggested price. You can change the price later.</span>
            </span>
        </label>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary" :disabled="form.processing">
                {{ product ? 'Save changes' : 'Add to catalog' }}
            </button>
            <Link :href="cancelUrl" class="btn-secondary">Cancel</Link>
        </div>
    </form>
</template>
