<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import OwnerLayout from '@/Layouts/OwnerLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

// Admins and shop owners share this page; each sees it inside their own layout.
// (No "delete account" section: users can't delete their own accounts.)
const page = usePage();
const Layout = computed(() => (page.props.auth.user.role === 'admin' ? AdminLayout : OwnerLayout));
</script>

<template>
    <Head title="Profile" />

    <component :is="Layout">
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900">Profile</h1>
            <p class="mt-1 text-sm text-gray-500">Your account details and password.</p>
        </template>

        <div class="mx-auto max-w-3xl space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <UpdateProfileInformationForm />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <UpdatePasswordForm />
            </div>
        </div>
    </component>
</template>
