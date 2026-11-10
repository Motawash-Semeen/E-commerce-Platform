<script setup>
import { Head, Link } from '@inertiajs/vue3';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';

// Shown by the 'has.shop' middleware on shop pages when the owner has no shop.
</script>

<template>
    <Head title="No shop yet" />

    <OwnerLayout>
        <div class="card mx-auto max-w-xl p-10 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-50 text-xl text-amber-600" aria-hidden="true">!</div>
            <h1 class="mt-4 text-lg font-semibold text-gray-900">You don't have a shop yet. Contact the admin.</h1>
            <p class="mt-2 text-sm text-gray-500">Once the admin creates your shop, you can browse the catalog and list products here.</p>
            <Link :href="route('owner.dashboard')" class="btn-primary mt-6">Back to dashboard</Link>
        </div>
    </OwnerLayout>
</template>
