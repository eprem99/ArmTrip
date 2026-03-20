<template>
    <header
        class="fixed top-0 z-30 border-b border-slate-200 bg-white/80 backdrop-blur-sm transition-[left,width] duration-200"
        :class="collapsed ? 'left-20 w-[calc(100%-5rem)]' : 'left-64 w-[calc(100%-16rem)]'"
    >
        <div class="mx-auto flex h-16 max-w-7xl w-full items-center justify-end px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm hover:bg-slate-50"
                    >
                        <BellIcon class="h-4 w-4" />
                    </button>
                    <a
                        href="/admin/settings"
                        class="hidden sm:inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm hover:bg-slate-50"
                        :title="t('admin.sidebar.settings')"
                    >
                        <Cog6ToothIcon class="h-4 w-4" />
                    </a>

                    <select
                        :value="localeRef"
                        class="hidden sm:block rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10"
                        @change="onLocaleChange"
                    >
                        <option value="en">EN</option>
                        <option value="ru">RU</option>
                        <option value="am">AM</option>
                    </select>

                    <div class="relative" data-user-menu>
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
                                <span class="text-xs font-medium text-slate-900">{{ t('admin.navbar.admin') }}</span>
                                <span class="text-[10px] text-slate-500">{{ t('admin.navbar.administrator') }}</span>
                            </span>
                            <ChevronDownIcon class="hidden h-3.5 w-3.5 text-slate-400 sm:inline" />
                        </button>

                        <div
                            v-if="userMenuOpen"
                            class="absolute right-0 mt-2 w-44 rounded-xl border border-slate-200 bg-white py-2 text-xs shadow-lg"
                        >
                            <a
                                :href="profileHref"
                                class="flex w-full items-center justify-between px-3 py-1.5 text-slate-700 hover:bg-slate-50"
                            >
                                <span>{{ t('admin.navbar.profile') }}</span>
                            </a>
                            <a
                                href="/admin/settings"
                                class="flex w-full items-center justify-between px-3 py-1.5 text-slate-700 hover:bg-slate-50 text-left"
                            >
                                <span>{{ t('admin.navbar.settings') }}</span>
                            </a>
                            <div class="my-1 border-t border-slate-100" />
                            <button
                                type="button"
                                class="flex w-full items-center justify-between px-3 py-1.5 text-red-600 hover:bg-red-50"
                            >
                                <span>{{ t('admin.navbar.sign_out') }}</span>
                            </button>
                        </div>
                    </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useI18n } from '../composables/useI18n';
import {
    BellIcon,
    Cog6ToothIcon,
    ChevronDownIcon,
} from '@heroicons/vue/24/outline';

defineProps({
    collapsed: { type: Boolean, default: false },
});

const { t } = useI18n();
const userMenuOpen = ref(false);

const localeRef = computed(() => typeof window !== 'undefined' ? window.__locale : 'en');

const profileHref = computed(() => {
    if (typeof window === 'undefined') return '/admin/users';
    const id = window.__userId;
    if (!id) return '/admin/users';
    return `/admin/users/${id}/edit`;
});

function onLocaleChange(e) {
    const val = e.target?.value;
    if (val && val !== localeRef.value) {
        window.location.href = `/locale/${val}`;
    }
}

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
