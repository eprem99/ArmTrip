<template>
    <aside
        :class="[
            'fixed inset-y-0 left-0 z-40 flex flex-col border-r border-slate-700/50 bg-slate-800 transition-[width] duration-200',
            collapsed ? 'w-20' : 'w-64',
        ]"
    >
        <!-- Logo -->
        <div
            :class="[
                'flex h-16 shrink-0 items-center border-b border-slate-700/50 transition-[padding] duration-200',
                collapsed ? 'justify-center px-0' : 'gap-2 px-4',
            ]"
        >
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-500 text-lg font-bold text-white shadow-sm"
            >
                SA
            </div>
            <div v-show="!collapsed" class="min-w-0 overflow-hidden">
                <p class="truncate text-xs font-semibold uppercase tracking-wide text-slate-400">
                    {{ t('admin.brand.stay_armenia') }}
                </p>
                <p class="truncate text-sm font-medium text-white">{{ t('admin.brand.armtrip_admin') }}</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav
            :class="[
                'flex-1 space-y-0.5 overflow-y-auto py-4 transition-[padding] duration-200',
                collapsed ? 'px-2' : 'px-3',
            ]"
        >
            <a
                href="/admin/dashboard"
                :class="[
                    'flex items-center rounded-xl py-2.5 text-sm font-medium transition-colors',
                    collapsed ? 'justify-center px-2' : 'gap-3 px-3',
                    isDashboard
                        ? 'bg-blue-500/20 text-blue-300'
                        : 'text-slate-300 hover:bg-slate-700/50 hover:text-white',
                ]"
                :title="collapsed ? t('admin.sidebar.dashboard') : undefined"
            >
                <Squares2X2Icon class="h-5 w-5 shrink-0" />
                <span v-show="!collapsed" class="truncate">{{ t('admin.sidebar.dashboard') }}</span>
            </a>
            <a
                href="/admin/media"
                :class="[
                    'flex items-center rounded-xl py-2.5 text-sm font-medium transition-colors',
                    collapsed ? 'justify-center px-2' : 'gap-3 px-3',
                    isMedia
                        ? 'bg-blue-500/20 text-blue-300'
                        : 'text-slate-300 hover:bg-slate-700/50 hover:text-white',
                ]"
                :title="collapsed ? t('admin.sidebar.media') : undefined"
            >
                <PhotoIcon class="h-5 w-5 shrink-0" />
                <span v-show="!collapsed" class="truncate">{{ t('admin.sidebar.media') }}</span>
            </a>
            <a
                href="/admin/content"
                :class="[
                    'flex items-center rounded-xl py-2.5 text-sm font-medium transition-colors',
                    collapsed ? 'justify-center px-2' : 'gap-3 px-3',
                    isContent
                        ? 'bg-blue-500/20 text-blue-300'
                        : 'text-slate-300 hover:bg-slate-700/50 hover:text-white',
                ]"
                :title="collapsed ? t('admin.sidebar.content') : undefined"
            >
                <DocumentTextIcon class="h-5 w-5 shrink-0" />
                <span v-show="!collapsed" class="truncate">{{ t('admin.sidebar.content') }}</span>
            </a>
            <div v-if="collapsed" class="space-y-0.5">
                <a
                    href="/admin/rentals"
                    :class="[
                        'flex items-center rounded-xl py-2.5 text-sm font-medium transition-colors',
                        'justify-center px-2',
                        isRentalsSection
                            ? 'bg-blue-500/20 text-blue-300'
                            : 'text-slate-300 hover:bg-slate-700/50 hover:text-white',
                    ]"
                    :title="t('admin.sidebar.rentals')"
                >
                    <BuildingOffice2Icon class="h-5 w-5 shrink-0" />
                </a>
            </div>
            <template v-else>
                <button
                    type="button"
                    :class="[
                        'flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium transition-colors',
                        rentalsOpen || isRentalsSection
                            ? 'bg-blue-500/20 text-blue-300'
                            : 'text-slate-300 hover:bg-slate-700/50 hover:text-white',
                    ]"
                    @click="rentalsOpen = !rentalsOpen"
                >
                    <BuildingOffice2Icon class="h-5 w-5 shrink-0" />
                    <span class="truncate">{{ t('admin.sidebar.rentals') }}</span>
                    <ChevronDownIcon
                        :class="['ml-auto h-4 w-4 shrink-0 transition-transform', rentalsOpen && 'rotate-180']"
                    />
                </button>
                <div v-show="rentalsOpen" class="mt-0.5 space-y-0.5 pl-4">
                    <a
                        href="/admin/rentals"
                        :class="[
                            'flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors',
                            isRentalsList
                                ? 'bg-blue-500/20 font-medium text-blue-300'
                                : 'text-slate-400 hover:bg-slate-700/50 hover:text-white',
                        ]"
                    >
                        <BuildingOffice2Icon class="h-4 w-4 shrink-0" />
                        <span class="truncate">{{ t('admin.sidebar.rentals_list') }}</span>
                    </a>
                    <a
                        href="/admin/rentals/types"
                        :class="[
                            'flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors',
                            isRentalsTypes
                                ? 'bg-blue-500/20 font-medium text-blue-300'
                                : 'text-slate-400 hover:bg-slate-700/50 hover:text-white',
                        ]"
                    >
                        <TagIcon class="h-4 w-4 shrink-0" />
                        <span class="truncate">{{ t('admin.sidebar.rental_types') }}</span>
                    </a>
                    <a
                        href="/admin/rentals/amenities"
                        :class="[
                            'flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors',
                            isRentalsAmenities
                                ? 'bg-blue-500/20 font-medium text-blue-300'
                                : 'text-slate-400 hover:bg-slate-700/50 hover:text-white',
                        ]"
                    >
                        <SparklesIcon class="h-4 w-4 shrink-0" />
                        <span class="truncate">{{ t('admin.sidebar.rental_amenities') }}</span>
                    </a>
                </div>
            </template>
            <div v-if="collapsed" class="space-y-0.5">
                <a
                    href="/admin/blog"
                    :class="[
                        'flex items-center rounded-xl py-2.5 text-sm font-medium transition-colors',
                        'justify-center px-2',
                        isBlog
                            ? 'bg-blue-500/20 text-blue-300'
                            : 'text-slate-300 hover:bg-slate-700/50 hover:text-white',
                    ]"
                    :title="t('admin.sidebar.blog')"
                >
                    <NewspaperIcon class="h-5 w-5 shrink-0" />
                </a>
            </div>
            <template v-else>
                <button
                    type="button"
                    :class="[
                        'flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium transition-colors',
                        isBlogOpen || isBlog
                            ? 'bg-blue-500/20 text-blue-300'
                            : 'text-slate-300 hover:bg-slate-700/50 hover:text-white',
                    ]"
                    @click="blogOpen = !blogOpen"
                >
                    <NewspaperIcon class="h-5 w-5 shrink-0" />
                    <span class="truncate">{{ t('admin.sidebar.blog') }}</span>
                    <ChevronDownIcon
                        :class="['ml-auto h-4 w-4 shrink-0 transition-transform', blogOpen && 'rotate-180']"
                    />
                </button>
                <div v-show="blogOpen" class="mt-0.5 space-y-0.5 pl-4">
                    <a
                        href="/admin/blog/posts"
                        :class="[
                            'flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors',
                            isBlogPosts
                                ? 'bg-blue-500/20 font-medium text-blue-300'
                                : 'text-slate-400 hover:bg-slate-700/50 hover:text-white',
                        ]"
                    >
                        <DocumentTextIcon class="h-4 w-4 shrink-0" />
                        <span class="truncate">{{ t('admin.sidebar.posts') }}</span>
                    </a>
                    <a
                        v-for="tax in blogTaxonomiesMenu"
                        :key="tax.slug"
                        :href="`/admin/blog/taxonomies/${tax.slug}`"
                        :class="[
                            'flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors',
                            activeTaxonomySlug === tax.slug
                                ? 'bg-blue-500/20 font-medium text-blue-300'
                                : 'text-slate-400 hover:bg-slate-700/50 hover:text-white',
                        ]"
                    >
                        <component
                            :is="getTaxonomyIconComponent(tax.icon)"
                            class="h-4 w-4 shrink-0"
                        />
                        <span class="truncate">{{ tax.name }}</span>
                    </a>
                </div>
            </template>
            <a
                href="/admin/users"
                :class="[
                    'flex items-center rounded-xl py-2.5 text-sm font-medium transition-colors',
                    collapsed ? 'justify-center px-2' : 'gap-3 px-3',
                    isUsers
                        ? 'bg-blue-500/20 text-blue-300'
                        : 'text-slate-300 hover:bg-slate-700/50 hover:text-white',
                ]"
                :title="t('admin.sidebar.users')"
            >
                <UsersIcon class="h-5 w-5 shrink-0" />
                <span v-show="!collapsed" class="truncate">{{ t('admin.sidebar.users') }}</span>
            </a>
            <div class="my-2 border-t border-slate-700/50" />
            <a
                href="/admin/settings"
                :class="[
                    'flex items-center rounded-xl py-2.5 text-sm font-medium transition-colors',
                    collapsed ? 'justify-center px-2' : 'gap-3 px-3',
                    isSettings
                        ? 'bg-blue-500/20 text-blue-300'
                        : 'text-slate-300 hover:bg-slate-700/50 hover:text-white',
                ]"
                :title="t('admin.sidebar.settings')"
            >
                <Cog6ToothIcon class="h-5 w-5 shrink-0" />
                <span v-show="!collapsed" class="truncate">{{ t('admin.sidebar.settings') }}</span>
            </a>
        </nav>

        <div class="shrink-0 border-t border-slate-700/50">
            <button
                type="button"
                :class="[
                    'flex w-full items-center gap-3 rounded-xl py-2.5 text-slate-400 transition-colors hover:bg-slate-700/50 hover:text-slate-200',
                    collapsed ? 'justify-center px-2' : 'px-3',
                ]"
                :title="collapsed ? t('admin.sidebar.expand') : t('admin.sidebar.collapse')"
                @click="$emit('update:collapsed', !collapsed)"
            >
                <ChevronLeftIcon
                    v-if="!collapsed"
                    class="h-5 w-5 shrink-0"
                />
                <ChevronRightIcon
                    v-else
                    class="h-5 w-5 shrink-0"
                />
                <span v-show="!collapsed" class="truncate text-sm font-medium">{{ t('admin.sidebar.collapse') }}</span>
            </button>
            <div
                v-show="!collapsed"
                class="px-4 pb-3"
            >
                <p class="text-[10px] text-slate-500">{{ t('admin.brand.armtrip_admin') }} v1.0</p>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useI18n } from '../composables/useI18n';
import {
    Squares2X2Icon,
    PhotoIcon,
    DocumentTextIcon,
    BuildingOffice2Icon,
    NewspaperIcon,
    UsersIcon,
    Cog6ToothIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    ChevronDownIcon,
    TagIcon,
    SparklesIcon,
} from '@heroicons/vue/24/outline';
import { getTaxonomyIconComponent } from '../data/taxonomyIcons';

defineProps({
    collapsed: { type: Boolean, default: false },
});
defineEmits(['update:collapsed']);

const { t } = useI18n();
const blogOpen = ref(false);
const rentalsOpen = ref(false);

const isDashboard = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname === '/admin/dashboard' || window.location.pathname === '/admin';
});

const isMedia = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname.startsWith('/admin/media');
});

const isContent = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname.startsWith('/admin/content');
});

const isRentalsSection = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname.startsWith('/admin/rentals');
});

const isRentalsList = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname === '/admin/rentals';
});

const isRentalsTypes = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname.startsWith('/admin/rentals/types');
});

const isRentalsAmenities = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname.startsWith('/admin/rentals/amenities');
});

const isUsers = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname.startsWith('/admin/users');
});

const isBlog = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname.startsWith('/admin/blog');
});

const isBlogPosts = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname === '/admin/blog/posts' || window.location.pathname === '/admin/blog';
});

const blogTaxonomiesMenu = computed(() => {
    if (typeof window === 'undefined' || !Array.isArray(window.__blogTaxonomies)) {
        return [];
    }
    return window.__blogTaxonomies;
});

const activeTaxonomySlug = computed(() => {
    if (typeof window === 'undefined') return null;
    const m = window.location.pathname.match(/^\/admin\/blog\/taxonomies\/([^/]+)\/?$/);
    return m ? m[1] : null;
});

const isSettings = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname.startsWith('/admin/settings');
});

onMounted(() => {
    if (typeof window !== 'undefined' && window.location.pathname.startsWith('/admin/blog')) {
        blogOpen.value = true;
    }
    if (typeof window !== 'undefined' && window.location.pathname.startsWith('/admin/rentals')) {
        rentalsOpen.value = true;
    }
});
</script>
