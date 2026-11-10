<script setup>
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Components/FormActions.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';

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
    <form @submit.prevent="submit">
        <FormSection title="Account" description="The owner logs in with this email address.">
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="form-label">Full name</label>
                    <input id="name" v-model="form.name" type="text" maxlength="255" class="form-input" required autofocus />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input id="email" v-model="form.email" type="email" maxlength="255" class="form-input" required autocomplete="off" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>
        </FormSection>

        <FormSection title="Password" :description="owner ? 'Leave both fields empty to keep the current password.' : 'At least 8 characters. Share it with the owner safely.'">
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="password" class="form-label">{{ owner ? 'New password' : 'Password' }}</label>
                    <input id="password" v-model="form.password" type="password" class="form-input" :required="!owner" autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
                <div>
                    <label for="password_confirmation" class="form-label">Confirm password</label>
                    <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="form-input" :required="!owner" autocomplete="new-password" />
                </div>
            </div>
        </FormSection>

        <FormSection title="Access" description="Turn the account off to block logging in without deleting it.">
            <ToggleSwitch v-model="form.is_active" label="Active account" description="Inactive accounts can't log in." />
            <InputError :message="form.errors.is_active" />
        </FormSection>

        <FormActions
            :cancel-href="route('admin.shop-owners.index')"
            :processing="form.processing"
            :dirty="form.isDirty"
            :submit-label="owner ? 'Save changes' : 'Create account'"
        />
    </form>
</template>
