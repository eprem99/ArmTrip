<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">{{ t('admin.content.title') }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ t('admin.content.description') }}
                </p>
            </div>
            <a
                :href="createPageHref"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                <PlusIcon class="h-5 w-5 shrink-0" />
                {{ t('admin.content.new') }}
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <p class="text-sm text-slate-600">
                    {{ filteredPages.length }} {{ t('admin.content.count') }}
                </p>
                <div class="relative ml-auto">
                    <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="search"
                        :placeholder="t('admin.content.search_placeholder')"
                        class="w-64 rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        @input="onSearchInput"
                    />
                </div>
            </div>

            <div v-if="loading" class="p-8 text-center text-sm text-slate-500">
                {{ t('admin.content.loading') }}
            </div>
            <div v-else-if="error" class="p-8 text-center text-sm text-red-600">
                {{ error }}
            </div>
            <AdminTable v-else>
                <template #head>
                    <th class="px-4 py-3">{{ t('admin.content.col_title') }}</th>
                    <th class="px-4 py-3">{{ t('admin.content.col_slug') }}</th>
                    <th class="px-4 py-3">{{ t('admin.content.col_status') }}</th>
                    <th class="px-4 py-3">{{ t('admin.content.col_sort_order') }}</th>
                    <th class="px-4 py-3">{{ t('admin.content.col_created_at') }}</th>
                    <th class="px-4 py-3">{{ t('admin.content.col_other_languages') }}</th>
                    <th class="px-4 py-3 text-right">{{ t('admin.content.action') }}</th>
                </template>

                <tr
                    v-for="row in pagedPages"
                    :key="row.translation_group_id || row.page?.id"
                    class="hover:bg-slate-50"
                >
                    <td class="px-4 py-3 font-medium text-slate-900">{{ row.page?.title || '—' }}</td>
                    <td class="px-4 py-3 text-slate-700 font-mono text-xs">{{ row.page?.slug ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span
                            class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                            :class="row.page?.status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                        >
                            {{ row.page?.status === 'published' ? t('admin.content.status_published') : t('admin.content.status_draft') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-700">{{ row.page?.sort_order ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ formatDate(row.page?.created_at) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <a
                                v-for="item in translationActions(row)"
                                :key="item.lcode"
                                :href="item.href"
                                class="inline-flex items-center gap-1 rounded-lg border bg-white px-2 py-1 text-xs font-medium text-slate-700 shadow-sm transition hover:border-[#2271b1] hover:text-[#2271b1] hover:shadow"
                                :class="item.isCreate ? 'border-dashed border-slate-300' : 'border-slate-200'"
                                :title="item.title"
                            >
                                <span class="font-mono text-[10px] uppercase tracking-wide text-slate-500">{{ item.lcode }}</span>
                                <PlusIcon v-if="item.isCreate" class="h-4 w-4 shrink-0" />
                                <PencilSquareIcon v-else class="h-4 w-4 shrink-0" />
                            </a>
                            <span v-if="translationActions(row).length === 0" class="text-xs text-slate-400">—</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a
                                :href="`/admin/content/pages/${row.page?.id}/edit`"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                :title="t('admin.content.edit')"
                            >
                                <PencilSquareIcon class="h-4 w-4" />
                            </a>
                            <button
                                type="button"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                                :title="t('admin.content.delete')"
                                @click="openDeleteModal(row.page)"
                            >
                                <TrashIcon class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="filteredPages.length === 0">
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                        {{ t('admin.content.empty') }}
                    </td>
                </tr>
            </AdminTable>
            <div
                v-if="!loading && !error && totalPages > 1"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-4 py-3"
            >
                <p class="text-sm text-slate-600">
                    {{ pageFrom }}–{{ pageTo }} {{ t('admin.content.pagination_of') }} {{ filteredPages.length }}
                </p>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        :disabled="currentPage <= 1"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(currentPage - 1)"
                    >
                        {{ t('admin.content.prev') }}
                    </button>
                    <span class="text-sm text-slate-500">
                        {{ currentPage }} / {{ totalPages }}
                    </span>
                    <button
                        type="button"
                        :disabled="currentPage >= totalPages"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(currentPage + 1)"
                    >
                        {{ t('admin.content.next') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete confirmation modal -->
        <Teleport to="body">
            <div
                v-if="deleteModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                aria-modal="true"
                role="dialog"
            >
                <div
                    class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
                    @click="closeDeleteModal"
                />
                <div
                    class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl ring-1 ring-slate-200"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <TrashIcon class="h-6 w-6 text-red-600" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">
                        {{ t('admin.content.delete_confirm_title') }}
                    </h3>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ t('admin.content.delete_confirm_message') }}
                        <span v-if="pageToDelete" class="font-medium text-slate-900">
                            «{{ pageToDelete?.title }}»
                        </span>?
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                            @click="closeDeleteModal"
                        >
                            {{ t('admin.content.delete_confirm_cancel') }}
                        </button>
                        <button
                            type="button"
                            :disabled="deleting"
                            class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-red-700 disabled:opacity-50"
                            @click="confirmDelete"
                        >
                            {{ deleting ? t('admin.content.loading') : t('admin.content.delete_confirm_confirm') }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '../../composables/useI18n';
import { PlusIcon, PencilSquareIcon, TrashIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import AdminTable from '../../components/AdminTable.vue';

const { t, locale } = useI18n();

/** Syncs with admin navbar locale (window.__locale) after /locale/xx redirect */
const listLang = computed(() => {
    if (typeof window === 'undefined') return 'en';
    return window.__locale || 'en';
});

const languageOptions = ref([]);

const pages = ref([]);
const searchQuery = ref('');
const searchDebounce = ref(null);
const currentPage = ref(1);
const perPage = ref(15);
const loading = ref(true);
const error = ref('');
const deleteModalOpen = ref(false);
const pageToDelete = ref(null);
const deleting = ref(false);

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

const createPageHref = computed(() => {
    const q = new URLSearchParams({ lang: listLang.value || 'en' });

    return `/admin/content/pages/create?${q.toString()}`;
});

/**
 * For each site language except the current UI language: edit existing translation or create URL.
 */
function translationActions(row) {
    const current = listLang.value;
    const gid = row.translation_group_id;
    if (!gid || !languageOptions.value.length) {
        return [];
    }

    return languageOptions.value
        .filter((l) => l.lcode !== current)
        .map((lang) => {
            const label = lang.native_name || lang.name || lang.lcode;
            const tr = (row.translations || []).find((x) => x.lcode === lang.lcode);
            if (tr) {
                return {
                    lcode: lang.lcode,
                    href: `/admin/content/pages/${tr.page_id}/edit`,
                    isCreate: false,
                    title: t('admin.content.edit_translation_title').replace(':lang', label),
                };
            }

            return {
                lcode: lang.lcode,
                href: `/admin/content/pages/create?lang=${encodeURIComponent(lang.lcode)}&translation_group=${encodeURIComponent(gid)}`,
                isCreate: true,
                title: t('admin.content.translation_create_title').replace(':lang', label),
            };
        });
}

function formatDate(value) {
    if (!value) return '—';
    try {
        const d = new Date(value);
        const loc = typeof locale === 'string' ? locale : locale?.value;
        const dateLoc = loc === 'ru' ? 'ru-RU' : loc === 'am' ? 'hy-AM' : 'en-US';
        return d.toLocaleDateString(dateLoc, {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    } catch {
        return value;
    }
}

async function loadLanguages() {
    setCsrf();
    try {
        const { data } = await axios.get('/admin/settings/api/languages');
        languageOptions.value = (data || []).filter((l) => l.status === 'active');
    } catch (_) {
        languageOptions.value = [];
    }
}

async function load() {
    setCsrf();
    loading.value = true;
    error.value = '';
    try {
        const { data } = await axios.get('/admin/content/api/pages', {
            params: { lang: listLang.value },
        });
        pages.value = data;
        currentPage.value = 1;
    } catch (e) {
        error.value = e.response?.data?.message || t('admin.content.load_error');
    } finally {
        loading.value = false;
    }
}

const filteredPages = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return pages.value;
    return pages.value.filter((row) => {
        const title = (row.page?.title || '').toLowerCase();
        const slug = (row.page?.slug || '').toLowerCase();
        return title.includes(q) || slug.includes(q);
    });
});

const totalPages = computed(() => {
    return Math.max(1, Math.ceil(filteredPages.value.length / perPage.value));
});

const pagedPages = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    return filteredPages.value.slice(start, start + perPage.value);
});

const pageFrom = computed(() => {
    if (filteredPages.value.length === 0) return 0;
    return (currentPage.value - 1) * perPage.value + 1;
});

const pageTo = computed(() => {
    return Math.min(filteredPages.value.length, currentPage.value * perPage.value);
});

function goToPage(page) {
    if (page < 1 || page > totalPages.value) return;
    currentPage.value = page;
}

function onSearchInput() {
    if (searchDebounce.value) clearTimeout(searchDebounce.value);
    searchDebounce.value = setTimeout(() => {
        currentPage.value = 1;
    }, 300);
}

function openDeleteModal(page) {
    pageToDelete.value = page;
    deleteModalOpen.value = true;
}

function closeDeleteModal() {
    if (!deleting.value) {
        deleteModalOpen.value = false;
        pageToDelete.value = null;
    }
}

async function confirmDelete() {
    if (!pageToDelete.value) return;
    setCsrf();
    deleting.value = true;
    try {
        await axios.delete(`/admin/content/api/pages/${pageToDelete.value.id}`);
        closeDeleteModal();
        await load();
    } catch (e) {
        error.value = e.response?.data?.message || t('admin.content.load_error');
    } finally {
        deleting.value = false;
    }
}

onMounted(async () => {
    await loadLanguages();
    await load();
});
</script>

