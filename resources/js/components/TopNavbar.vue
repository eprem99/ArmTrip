<template>
    <header
        class="fixed inset-x-0 top-0 z-30 border-b border-slate-200 bg-white/80 backdrop-blur-sm"
    >
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2">
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-sm font-semibold text-white shadow-sm"
                >
                    SA
                </div>
                <div class="hidden sm:block">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Stay Armenia
                    </p>
                    <p class="text-sm font-medium text-slate-900">Admin Dashboard</p>
                </div>
            </div>

            <div class="flex flex-1 items-center justify-end gap-4">
                <div class="hidden md:flex flex-1 max-w-md items-center">
                    <div class="relative w-full">
                        <span
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
                        >
                            <MagnifyingGlassIcon class="h-4 w-4" />
                        </span>
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Search reports, customers..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/10"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm hover:bg-slate-50"
                    >
                        <BellIcon class="h-4 w-4" />
                    </button>
                    <button
                        type="button"
                        class="hidden sm:inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm hover:bg-slate-50"
                    >
                        <Cog6ToothIcon class="h-4 w-4" />
                    </button>

                    <select
                        v-model="language"
                        class="hidden sm:block rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10"
                    >
                        <option value="en">EN</option>
                        <option value="ru">RU</option>
                        <option value="hy">HY</option>
                    </select>

                    <div class="relative">
                        <button
                            type="button"
                            class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 shadow-sm hover:bg-slate-50"
                            @click="toggleUserMenu"
                        >
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700"
                            >
                                AD
                            </span>
                            <span class="hidden sm:flex flex-col items-start">
                                <span class="text-xs font-medium text-slate-900">Admin</span>
                                <span class="text-[10px] text-slate-500">Administrator</span>
                            </span>
                            <ChevronDownIcon class="hidden h-3.5 w-3.5 text-slate-400 sm:inline" />
                        </button>

                        <div
                            v-if="userMenuOpen"
                            class="absolute right-0 mt-2 w-44 rounded-xl border border-slate-200 bg-white py-2 text-xs shadow-lg"
                        >
                            <button
                                type="button"
                                class="flex w-full items-center justify-between px-3 py-1.5 text-slate-700 hover:bg-slate-50"
                            >
                                <span>Profile</span>
                            </button>
                            <button
                                type="button"
                                class="flex w-full items-center justify-between px-3 py-1.5 text-slate-700 hover:bg-slate-50"
                            >
                                <span>Settings</span>
                            </button>
                            <div class="my-1 border-t border-slate-100" />
                            <button
                                type="button"
                                class="flex w-full items-center justify-between px-3 py-1.5 text-red-600 hover:bg-red-50"
                            >
                                <span>Sign out</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import {
    BellIcon,
    Cog6ToothIcon,
    MagnifyingGlassIcon,
    ChevronDownIcon,
} from '@heroicons/vue/24/outline';

const search = ref('');
const language = ref('en');
const userMenuOpen = ref(false);

const handleClickOutside = (event) => {
    const dropdown = document.querySelector('[data-user-menu]');
    if (dropdown && !dropdown.contains(event.target)) {
        userMenuOpen.value = false;
    }
};

const toggleUserMenu = () => {
    userMenuOpen.value = !userMenuOpen.value;
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

