<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import ImagePreview from '@/Components/ImagePreview.vue';
import InputError from '@/Components/InputError.vue';
import { formatPrice } from '@/utils/format';

/**
 * The shop's own price, details and thumbnail for a catalog product.
 * Add (no listing): prefilled from the catalog. Edit: the listing's values.
 */
const props = defineProps({
    product: { type: Object, required: true },   // catalog values: { id, slug, name, description, new_price, old_price, image_url, category, subcategory }
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
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Catalog reference -->
        <aside class="card h-fit overflow-hidden lg:order-last">
            <img :src="product.image_url" :alt="product.name" class="aspect-[4/3] w-full object-cover" />
            <div class="space-y-2 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">From the catalog</p>
                <h2 class="font-semibold text-gray-900">{{ product.name }}</h2>
                <p class="text-sm text-gray-500">{{ product.category }} › {{ product.subcategory }}</p>
                <p class="text-sm text-gray-600">Suggested price: <span class="font-semibold text-gray-900">{{ formatPrice(product.new_price) }}</span></p>
                <p class="line-clamp-4 whitespace-pre-line text-sm text-gray-500">{{ product.description }}</p>
            </div>
        </aside>

        <form class="card space-y-6 p-6 lg:col-span-2" @submit.prevent="submit">
            <InputError v-if="form.errors.product_id" :message="form.errors.product_id" />

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="price" class="form-label">Your price (Tk)</label>
                    <input id="price" v-model="form.price" type="number" min="0" step="0.01" class="form-input" required />
                    <InputError class="mt-2" :message="form.errors.price" />
                </div>
                <div>
                    <label for="old_price" class="form-label">Old price (Tk) <span class="font-normal text-gray-400">(optional)</span></label>
                    <input id="old_price" v-model="form.old_price" type="number" min="0" step="0.01" class="form-input" />
                    <p class="form-hint">Shown struck through. Must be at least your price.</p>
                    <InputError class="mt-2" :message="form.errors.old_price" />
                </div>
            </div>

            <div>
                <label for="description" class="form-label">Your description <span class="font-normal text-gray-400">(optional)</span></label>
                <textarea id="description" v-model="form.description" rows="5" maxlength="5000" class="form-input" placeholder="Leave empty to use the catalog description." />
                <InputError class="mt-2" :message="form.errors.description" />
            </div>

            <div>
                <label for="thumbnail" class="form-label">Your thumbnail <span class="font-normal text-gray-400">(optional)</span></label>
                <ImagePreview id="thumbnail" v-model="form.thumbnail" :current-url="listing?.thumbnail_url ?? product.image_url" />
                <p class="form-hint">Without your own image, customers see the catalog image.</p>
                <InputError class="mt-2" :message="form.errors.thumbnail" />
            </div>

            <label class="flex items-start gap-3 rounded-xl border border-gray-200 p-4">
                <input v-model="form.is_active" type="checkbox" class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                <span>
                    <span class="block text-sm font-medium text-gray-900">Visible in my shop</span>
                    <span class="block text-xs text-gray-500">Untick to hide it from customers without removing it.</span>
                </span>
            </label>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary" :disabled="form.processing">
                    {{ listing ? 'Save changes' : 'Add to my shop' }}
                </button>
                <Link :href="listing ? route('owner.listings.index') : route('owner.catalog.index')" class="btn-secondary">Cancel</Link>
            </div>
        </form>
    </div>
</template>
