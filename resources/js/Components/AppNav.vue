<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import BrandMark from '@/Components/BrandMark.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

/**
 * Top navigation shared by the Admin, Owner and Public layouts.
 * links: [{ label, href, active, inertia }] — `inertia: false` for pages that are
 * still plain Blade (they need a full page load, not an Inertia visit).
 */
defineProps({
    links: { type: Array, default: () => [] },
});

const page = usePage();
const user = computed(() => page.props.auth.user);   // only id, name, email, role are shared
const open = ref(false);

const linkClass = (active) =>
    [
        'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium transition',
        active ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
    ].join(' ');
</script>

<template>
    <nav class="sticky top-0 z-40 border-b border-gray-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <Link :href="user ? route('dashboard') : '/'" class="shrink-0">
                <BrandMark />
            </Link>

            <!-- Desktop links -->
            <div class="hidden flex-1 items-center gap-1 lg:flex">
                <template v-for="link in links" :key="link.label">
                    <Link v-if="link.inertia !== false" :href="link.href" :class="linkClass(link.active)">{{ link.label }}</Link>
                    <a v-else :href="link.href" :class="linkClass(link.active)">{{ link.label }}</a>
                </template>
            </div>

            <div class="hidden items-center lg:flex">
                <Dropdown v-if="user" align="right" width="48">
                    <template #trigger>
                        <button type="button" class="inline-flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 font-bold text-indigo-700">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </span>
                            {{ user.name }}
                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </template>
                    <template #content>
                        <div class="px-4 py-2 text-xs text-gray-500">
                            Signed in as {{ user.role === 'admin' ? 'admin' : 'shop owner' }}
                        </div>
                        <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">Log out</DropdownLink>
                    </template>
                </Dropdown>
                <Link v-else :href="route('login')" class="rounded-lg bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                    Log in
                </Link>
            </div>

            <!-- Mobile toggle -->
            <button
                type="button"
                class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden"
                :aria-expanded="open"
                aria-label="Toggle navigation"
                @click="open = !open"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path v-if="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile menu -->
        <div v-show="open" class="space-y-1 border-t border-gray-100 px-4 py-3 lg:hidden">
            <template v-for="link in links" :key="link.label">
                <Link v-if="link.inertia !== false" :href="link.href" :class="linkClass(link.active)" class="flex">{{ link.label }}</Link>
                <a v-else :href="link.href" :class="linkClass(link.active)" class="flex">{{ link.label }}</a>
            </template>
            <div class="mt-2 border-t border-gray-100 pt-2">
                <template v-if="user">
                    <Link :href="route('profile.edit')" :class="linkClass(false)" class="flex">Profile</Link>
                    <Link :href="route('logout')" method="post" as="button" :class="linkClass(false)" class="flex w-full">Log out</Link>
                </template>
                <Link v-else :href="route('login')" :class="linkClass(false)" class="flex">Log in</Link>
            </div>
        </div>
    </nav>
</template>
