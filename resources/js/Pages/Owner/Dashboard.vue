<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({
    shop: { type: Object, default: null },   // { name, slug, is_active, listings_count, active_listings_count } or null
    myProductsCount: { type: Number, required: true },
});

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="My dashboard" />

    <OwnerLayout>
        <template #header>
            <PageHeader title="My dashboard" :subtitle="`Welcome, ${user.name}.`">
                <Link v-if="shop?.is_active" :href="route('shops.show', shop.slug)" class="btn-secondary">View my storefront</Link>
            </PageHeader>
        </template>

        <template v-if="shop">
            <div class="card relative overflow-hidden p-6">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-500" />
                <p class="text-sm font-medium text-gray-500">Your shop</p>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <h2 class="text-2xl font-bold text-gray-900">{{ shop.name }}</h2>
                    <StatusBadge :active="shop.is_active" />
                </div>
                <p v-if="!shop.is_active" class="mt-3 text-sm text-amber-700">
                    Your shop is inactive, so customers can't see it. Contact the admin to activate it.
                </p>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="card p-6">
                    <p class="text-sm font-medium text-gray-500">Products in my shop</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">{{ shop.listings_count }}</p>
                </div>
                <div class="card p-6">
                    <p class="text-sm font-medium text-gray-500">Visible to customers</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">{{ shop.is_active ? shop.active_listings_count : 0 }}</p>
                </div>
                <div class="card p-6">
                    <p class="text-sm font-medium text-gray-500">Products I added to the catalog</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">{{ myProductsCount }}</p>
                </div>
            </div>

            <div class="card mt-6 p-6">
                <h2 class="text-base font-semibold text-gray-900">Quick links</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    <Link :href="route('owner.catalog.index')" class="btn-primary">Browse catalog</Link>
                    <Link :href="route('owner.listings.index')" class="btn-secondary">My listings</Link>
                    <Link :href="route('owner.products.create')" class="btn-secondary">Create a product</Link>
                    <Link :href="route('owner.products.index')" class="btn-secondary">My products</Link>
                </div>
            </div>
        </template>

        <div v-else class="card p-10 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-50 text-xl text-amber-600" aria-hidden="true">!</div>
            <h2 class="mt-4 text-lg font-semibold text-gray-900">You don't have a shop yet</h2>
            <p class="mt-2 text-sm text-gray-500">Contact the admin to set up your shop. You can still add products to the shared catalog.</p>
            <div class="mt-6 flex justify-center gap-2">
                <Link :href="route('owner.products.create')" class="btn-primary">Create a product</Link>
                <Link :href="route('profile.edit')" class="btn-secondary">Edit your profile</Link>
            </div>
        </div>
    </OwnerLayout>
</template>
