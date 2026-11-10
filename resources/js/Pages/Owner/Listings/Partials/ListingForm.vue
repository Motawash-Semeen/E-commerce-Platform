<script setup>
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Components/FormActions.vue';
import FormSection from '@/Components/FormSection.vue';
import ImagePreview from '@/Components/ImagePreview.vue';
import InputError from '@/Components/InputError.vue';
import MoneyInput from '@/Components/MoneyInput.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';
import { formatPrice } from '@/utils/format';

/**
 * The shop's own price, details and thumbnail for a catalog product.
 * Add (no listing): prefilled from the catalog. Edit: the listing's values.
 */
const props = defineProps({
    product: { type: Object, required: true },   // catalog values: { slug, name, description, new_price, old_price, image_url, category, subcategory }
    listing: { type: Object, default: null },    // { id, price, old_price, description, is_active, thumbnail_url }
});

const form = useForm({
    price: props.listing?.price ?? props.product.new_price,
    old_price: props.listing ? (props.listing.old_price ?? '') : (props.product.old_price ?? ''),
    description: props.listing?.description ?? '',
    thumbnail: null,
    is_active: props.listing?.is_active ?? true,
});

function submit() {
    if (props.listing) {
        form.transform((data) => ({ ...data, _method: 'put' }))
            .post(route('owner.listings.update', props.listing.id), { forceFormData: true });
    } else {
        form.post(route('owner.catalog.store', props.product.slug), { forceFormData: true });
    }
}
</script>

<template>
    <form @submit.prevent="submit">
        <!-- Catalog reference -->
        <div class="card mb-8 flex flex-col overflow-hidden sm:flex-row">
            <img :src="product.image_url" :alt="product.name" class="h-40 w-full object-cover sm:h-auto sm:w-48" />
            <div class="space-y-1 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">From the catalog</p>
                <h2 class="text-lg font-semibold text-gray-900">{{ product.name }}</h2>
                <p class="text-sm text-gray-500">{{ product.category }} › {{ product.subcategory }} · Suggested <span class="font-semibold text-gray-900">{{ formatPrice(product.new_price) }}</span></p>
                <p class="line-clamp-2 whitespace-pre-line text-sm text-gray-500">{{ product.description }}</p>
            </div>
        </div>

        <InputError v-if="form.errors.product_id" class="mb-6" :message="form.errors.product_id" />

        <FormSection title="Your price" description="What your shop charges. Other shops keep their own prices.">
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="price" class="form-label">Price</label>
                    <MoneyInput id="price" v-model="form.price" required />
                    <InputError class="mt-2" :message="form.errors.price" />
                </div>
                <div>
                    <label for="old_price" class="form-label">Old price <span class="font-normal text-gray-400">(optional)</span></label>
                    <MoneyInput id="old_price" v-model="form.old_price" />
                    <p class="form-hint">Shown struck through. Must be at least your price.</p>
                    <InputError class="mt-2" :message="form.errors.old_price" />
                </div>
            </div>
        </FormSection>

        <FormSection title="Your details" description="Optional. Leave them empty to use the catalog description and image.">
            <div>
                <label for="description" class="form-label">Description</label>
                <textarea id="description" v-model="form.description" rows="5" maxlength="5000" class="form-input" placeholder="Leave empty to use the catalog description." />
                <InputError class="mt-2" :message="form.errors.description" />
            </div>
            <div>
                <label for="thumbnail" class="form-label">Thumbnail</label>
                <ImagePreview id="thumbnail" v-model="form.thumbnail" :current-url="listing?.thumbnail_url ?? product.image_url" />
                <InputError class="mt-2" :message="form.errors.thumbnail" />
            </div>
        </FormSection>

        <FormSection title="Visibility" description="Hide a product without removing it from your shop.">
            <ToggleSwitch v-model="form.is_active" label="Visible in my shop" description="Customers can see and open this product." />
        </FormSection>

        <FormActions
            :cancel-href="listing ? route('owner.listings.index') : route('owner.catalog.index')"
            :processing="form.processing"
            :dirty="form.isDirty"
            :submit-label="listing ? 'Save changes' : 'Add to my shop'"
        />
    </form>
</template>
