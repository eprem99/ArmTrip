<template>
    <aside
        :class="[
            'fixed inset-y-0 left-0 z-40 flex flex-col border-r border-slate-200 bg-white transition-[width] duration-200',
            collapsed ? 'w-20' : 'w-64',
        ]"
    >
        <!-- Logo -->
        <div
            :class="[
                'flex h-16 shrink-0 items-center border-b border-slate-200 transition-[padding] duration-200',
                collapsed ? 'justify-center px-0' : 'gap-2 px-4',
            ]"
        >
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-lg font-bold text-white shadow-sm"
            >
                SA
            </div>
            <div v-show="!collapsed" class="min-w-0 overflow-hidden">
                <p class="truncate text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Stay Armenia
                </p>
                <p class="truncate text-sm font-medium text-slate-900">ArmTrip Admin</p>
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
                        ? 'bg-blue-50 text-blue-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                ]"
                :title="collapsed ? 'Дашборд' : undefined"
            >
                <Squares2X2Icon class="h-5 w-5 shrink-0" />
                <span v-show="!collapsed" class="truncate">Дашборд</span>
            </a>
            <a
                href="#"
                :class="[
                    'flex items-center rounded-xl py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-900',
                    collapsed ? 'justify-center px-2' : 'gap-3 px-3',
                ]"
                title="Заказы"
            >
                <ShoppingBagIcon class="h-5 w-5 shrink-0" />
                <span v-show="!collapsed" class="truncate">Заказы</span>
            </a>
            <a
                href="#"
                :class="[
                    'flex items-center rounded-xl py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-900',
                    collapsed ? 'justify-center px-2' : 'gap-3 px-3',
                ]"
                title="Контент"
            >
                <DocumentTextIcon class="h-5 w-5 shrink-0" />
                <span v-show="!collapsed" class="truncate">Контент</span>
            </a>
            <!-- Блог (раскрывающийся) -->
            <div v-if="collapsed" class="space-y-0.5">
                <a
                    href="/admin/blog"
                    :class="[
                        'flex items-center rounded-xl py-2.5 text-sm font-medium transition-colors',
                        'justify-center px-2',
                        isBlog
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    title="Блог"
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
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    @click="blogOpen = !blogOpen"
                >
                    <NewspaperIcon class="h-5 w-5 shrink-0" />
                    <span class="truncate">Блог</span>
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
                                ? 'bg-blue-50 font-medium text-blue-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <DocumentTextIcon class="h-4 w-4 shrink-0" />
                        <span class="truncate">Посты</span>
                    </a>
                    <a
                        href="/admin/blog/categories"
                        :class="[
                            'flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors',
                            isBlogCategories
                                ? 'bg-blue-50 font-medium text-blue-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <TagIcon class="h-4 w-4 shrink-0" />
                        <span class="truncate">Категории</span>
                    </a>
                </div>
            </template>
            <a
                href="#"
                :class="[
                    'flex items-center rounded-xl py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-900',
                    collapsed ? 'justify-center px-2' : 'gap-3 px-3',
                ]"
                title="Пользователи"
            >
                <UsersIcon class="h-5 w-5 shrink-0" />
                <span v-show="!collapsed" class="truncate">Пользователи</span>
            </a>
            <div class="my-2 border-t border-slate-100" />
            <a
                href="/admin/settings"
                :class="[
                    'flex items-center rounded-xl py-2.5 text-sm font-medium transition-colors',
                    collapsed ? 'justify-center px-2' : 'gap-3 px-3',
                    isSettings
                        ? 'bg-blue-50 text-blue-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                ]"
                title="Настройки"
            >
                <Cog6ToothIcon class="h-5 w-5 shrink-0" />
                <span v-show="!collapsed" class="truncate">Настройки</span>
            </a>
        </nav>

        <!-- Toggle + Footer -->
        <div class="shrink-0 border-t border-slate-200">
            <button
                type="button"
                :class="[
                    'flex w-full items-center gap-3 rounded-xl py-2.5 text-slate-500 transition-colors hover:bg-slate-50 hover:text-slate-700',
                    collapsed ? 'justify-center px-2' : 'px-3',
                ]"
                :title="collapsed ? 'Развернуть меню' : 'Свернуть меню'"
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
                <span v-show="!collapsed" class="truncate text-sm font-medium">Свернуть</span>
            </button>
            <div
                v-show="!collapsed"
                class="px-4 pb-3"
            >
                <p class="text-[10px] text-slate-400">ArmTrip Admin v1.0</p>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import {
    Squares2X2Icon,
    ShoppingBagIcon,
    DocumentTextIcon,
    NewspaperIcon,
    UsersIcon,
    Cog6ToothIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    ChevronDownIcon,
    TagIcon,
} from '@heroicons/vue/24/outline';

defineProps({
    collapsed: { type: Boolean, default: false },
});
defineEmits(['update:collapsed']);

const blogOpen = ref(false);

const isDashboard = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname === '/admin/dashboard' || window.location.pathname === '/admin';
});

const isBlog = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname.startsWith('/admin/blog');
});

const isBlogPosts = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname === '/admin/blog/posts' || window.location.pathname === '/admin/blog';
});

const isBlogCategories = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname === '/admin/blog/categories';
});

const isSettings = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.location.pathname.startsWith('/admin/settings');
});

onMounted(() => {
    if (typeof window !== 'undefined' && window.location.pathname.startsWith('/admin/blog')) {
        blogOpen.value = true;
    }
});
</script>
