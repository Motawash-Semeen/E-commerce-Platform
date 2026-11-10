<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Components/FormActions.vue';
import FormSection from '@/Components/FormSection.vue';
import ImagePreview from '@/Components/ImagePreview.vue';
import InputError from '@/Components/InputError.vue';
import MoneyInput from '@/Components/MoneyInput.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';

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
    <form @submit.prevent="submit">
        <FormSection title="Product details" description="Name, category and description shown in the catalog. Shops can write their own description later.">
            <div>
                <label for="name" class="form-label">Product name</label>
                <input id="name" v-model="form.name" type="text" maxlength="150" class="form-input" required />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

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
                <label for="description" class="form-label">Description</label>
                <textarea id="description" v-model="form.description" rows="5" maxlength="5000" class="form-input" required />
                <p class="form-hint">Line breaks are kept.</p>
                <InputError class="mt-2" :message="form.errors.description" />
            </div>
        </FormSection>

        <FormSection title="Pricing" description="A suggested price. Each shop sets its own price when it lists the product.">
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="new_price" class="form-label">Suggested price</label>
                    <MoneyInput id="new_price" v-model="form.new_price" required />
                    <InputError class="mt-2" :message="form.errors.new_price" />
                </div>
                <div>
                    <label for="old_price" class="form-label">Old price <span class="font-normal text-gray-400">(optional)</span></label>
                    <MoneyInput id="old_price" v-model="form.old_price" />
                    <p class="form-hint">Shown struck through when higher than the price.</p>
                    <InputError class="mt-2" :message="form.errors.old_price" />
                </div>
            </div>
        </FormSection>

        <FormSection title="Image" :description="product ? 'Leave empty to keep the current image.' : 'The catalog photo. Shops can upload their own thumbnail.'">
            <ImagePreview id="image" v-model="form.image" :current-url="product?.image_url" />
            <InputError :message="form.errors.image" />
        </FormSection>

        <FormSection v-if="canAddToShop" title="My shop" description="Save a step by listing it in your shop now.">
            <ToggleSwitch v-model="form.add_to_shop" label="Also add to my shop" description="Listed at the suggested price. You can change it later." />
        </FormSection>

        <FormActions
            :cancel-href="cancelUrl"
            :processing="form.processing"
            :dirty="form.isDirty"
            :submit-label="product ? 'Save changes' : 'Add to catalog'"
        />
    </form>
</template>
