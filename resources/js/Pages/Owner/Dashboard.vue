<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';

defineProps({
    shop: { type: Object, default: null },   // { name, slug, is_active } or null
});

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="My dashboard" />

    <OwnerLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900">My dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Welcome, {{ user.name }}.</p>
        </template>

        <div v-if="shop" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Your shop</p>
            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-900">{{ shop.name }}</h2>
                <span
                    class="rounded-full px-2.5 py-0.5 text-xs font-semibold"
                    :class="shop.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700'"
                >
                    {{ shop.is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <p class="mt-3 text-sm text-gray-500">Catalog browsing and your listings arrive in the next steps.</p>
        </div>

        <div v-else class="rounded-2xl border border-gray-200 bg-white p-10 text-center shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">You don't have a shop yet</h2>
            <p class="mt-2 text-sm text-gray-500">Contact the admin to set up your shop.</p>
            <Link :href="route('profile.edit')" class="mt-6 inline-flex rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Edit your profile
            </Link>
        </div>
    </OwnerLayout>
</template>
