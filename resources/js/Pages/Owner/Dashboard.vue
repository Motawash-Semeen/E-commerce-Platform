<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import AnimatedNumber from '@/Components/AnimatedNumber.vue';
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
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-500 bg-[length:200%_200%] p-6 text-white shadow-xl shadow-indigo-500/20 animate-gradient">
                <div class="absolute -right-10 -top-16 h-48 w-48 rounded-full bg-white/15 blur-2xl animate-blob" aria-hidden="true" />
                <p class="relative text-sm font-medium text-indigo-100">Your shop</p>
                <div class="relative mt-2 flex flex-wrap items-center gap-3">
                    <h2 class="text-3xl font-extrabold">{{ shop.name }}</h2>
                    <StatusBadge :active="shop.is_active" />
                </div>
                <p v-if="!shop.is_active" class="relative mt-3 text-sm font-medium text-amber-100">
                    Your shop is inactive, so customers can't see it. Contact the admin to activate it.
                </p>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="card card-hover animate-fade-up p-6 [animation-delay:80ms]">
                    <p class="text-sm font-medium text-gray-500">Products in my shop</p>
                    <p class="mt-2 text-4xl font-extrabold tracking-tight text-gray-900"><AnimatedNumber :value="shop.listings_count" /></p>
                </div>
                <div class="card card-hover animate-fade-up p-6 [animation-delay:160ms]">
                    <p class="text-sm font-medium text-gray-500">Visible to customers</p>
                    <p class="mt-2 text-4xl font-extrabold tracking-tight text-gray-900"><AnimatedNumber :value="shop.is_active ? shop.active_listings_count : 0" /></p>
                </div>
                <div class="card card-hover animate-fade-up p-6 [animation-delay:240ms]">
                    <p class="text-sm font-medium text-gray-500">Products I added to the catalog</p>
                    <p class="mt-2 text-4xl font-extrabold tracking-tight text-gray-900"><AnimatedNumber :value="myProductsCount" /></p>
                </div>
            </div>

            <div class="card mt-6 animate-fade-up p-6 [animation-delay:320ms]">
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
