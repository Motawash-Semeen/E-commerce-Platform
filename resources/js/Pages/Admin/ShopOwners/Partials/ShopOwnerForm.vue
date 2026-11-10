<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

// Shared by Create and Edit. On edit the password is optional (blank keeps it).
const props = defineProps({
    owner: { type: Object, default: null },
});

const form = useForm({
    name: props.owner?.name ?? '',
    email: props.owner?.email ?? '',
    password: '',
    password_confirmation: '',
    is_active: props.owner?.is_active ?? true,
});

function submit() {
    const options = { onFinish: () => form.reset('password', 'password_confirmation') };

    if (props.owner) {
        form.put(route('admin.shop-owners.update', props.owner.id), options);
    } else {
        form.post(route('admin.shop-owners.store'), options);
    }
}
</script>

<template>
    <form class="card max-w-2xl space-y-6 p-6" @submit.prevent="submit">
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label for="name" class="form-label">Name</label>
                <input id="name" v-model="form.name" type="text" maxlength="255" class="form-input" required autofocus />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <label for="email" class="form-label">Email</label>
                <input id="email" v-model="form.email" type="email" maxlength="255" class="form-input" required autocomplete="off" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <label for="password" class="form-label">{{ owner ? 'New password' : 'Password' }}</label>
                <input id="password" v-model="form.password" type="password" class="form-input" :required="!owner" autocomplete="new-password" />
                <p class="form-hint">{{ owner ? 'Leave blank to keep the current password.' : 'At least 8 characters.' }}</p>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <label for="password_confirmation" class="form-label">Confirm password</label>
                <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="form-input" :required="!owner" autocomplete="new-password" />
            </div>
        </div>

        <label class="flex items-start gap-3 rounded-xl border border-gray-200 p-4">
            <input v-model="form.is_active" type="checkbox" class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
            <span>
                <span class="block text-sm font-medium text-gray-900">Active</span>
                <span class="block text-xs text-gray-500">Inactive accounts can't log in.</span>
            </span>
        </label>
        <InputError v-if="form.errors.is_active" :message="form.errors.is_active" />

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary" :disabled="form.processing">
                {{ owner ? 'Save changes' : 'Create account' }}
            </button>
            <Link :href="route('admin.shop-owners.index')" class="btn-secondary">Cancel</Link>
        </div>
    </form>
</template>
