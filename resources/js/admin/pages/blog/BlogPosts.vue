<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">{{ t('admin.blog_posts.title') }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ t('admin.blog_posts.description') }}
                </p>
            </div>
            <a
                :href="createPostHref"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                <PlusIcon class="h-5 w-5 shrink-0" />
                {{ t('admin.blog_posts.new') }}
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <p class="text-sm text-slate-600">
                    {{ filteredRows.length }} {{ t('admin.blog_posts.count') }}
                </p>
                <div class="relative">
                    <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="search"
                        :placeholder="t('admin.blog_posts.search_placeholder')"
                        class="w-64 rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        @input="onSearchInput"
                    />
                </div>
            </div>

            <div v-if="loading" class="p-8 text-center text-sm text-slate-500">
                {{ t('admin.blog_posts.loading') }}
            </div>
            <div v-else-if="error" class="p-8 text-center text-sm text-red-600">
                {{ error }}
            </div>
            <AdminTable v-else>
                <template #head>
                    <th class="px-4 py-3">{{ t('admin.blog_posts.col_title') }}</th>
                    <th class="px-4 py-3">{{ t('admin.blog_posts.col_slug') }}</th>
                    <th class="px-4 py-3">{{ t('admin.blog_posts.col_status') }}</th>
                    <th class="px-4 py-3">{{ t('admin.blog_posts.col_created_at') }}</th>
                    <th class="px-4 py-3">{{ t('admin.content.col_other_languages') }}</th>
                    <th class="px-4 py-3 text-right">{{ t('admin.blog_posts.action') }}</th>
                </template>

                <tr
                    v-for="row in pagedRows"
                    :key="row.translation_group_id || row.post?.id"
                    class="hover:bg-slate-50"
                >
                    <td class="px-4 py-3 font-medium text-slate-900">{{ row.post?.title }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ row.post?.slug }}</td>
                    <td class="px-4 py-3">
                        <span
                            class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                            :class="row.post?.status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                        >
                            {{ row.post?.status === 'published' ? t('admin.blog_posts.status_published') : t('admin.blog_posts.status_draft') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ formatDate(row.post?.created_at) }}</td>
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
                                :href="`/admin/blog/posts/${row.post?.id}/edit`"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                :title="t('admin.blog_posts.edit')"
                            >
                                <PencilSquareIcon class="h-4 w-4" />
                            </a>
                            <button
                                type="button"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                                :title="t('admin.blog_posts.delete')"
                                @click="openDeleteModal(row.post)"
                            >
                                <TrashIcon class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="filteredRows.length === 0">
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                        {{ t('admin.blog_posts.empty') }}
                    </td>
                </tr>
            </AdminTable>

            <div
                v-if="!loading && !error && totalPages > 1"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-4 py-3"
            >
                <p class="text-sm text-slate-600">
                    {{ pageFrom }}–{{ pageTo }} {{ t('admin.blog_posts.pagination_of') }} {{ filteredRows.length }}
                </p>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        :disabled="currentPage <= 1"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 disabled:pointer-events-none disabled:opacity-50"
                        @click="goToPage(currentPage - 1)"
                    >
                        {{ t('admin.blog_posts.prev') }}
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
                        {{ t('admin.blog_posts.next') }}
                    </button>
                </div>
            </div>
        </div>

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
                        {{ t('admin.blog_posts.delete_confirm_title') }}
                    </h3>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ t('admin.blog_posts.delete_confirm_message') }}
                        <span v-if="postToDelete" class="font-medium text-slate-900">
                            «{{ postToDelete.title }}»
                        </span>?
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                            @click="closeDeleteModal"
                        >
                            {{ t('admin.blog_posts.delete_confirm_cancel') }}
                        </button>
                        <button
                            type="button"
                            :disabled="deleting"
                            class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-red-700 disabled:opacity-50"
                            @click="confirmDelete"
                        >
                            {{ deleting ? t('admin.blog_posts.loading') : t('admin.blog_posts.delete_confirm_confirm') }}
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

const listLang = computed(() => {
    if (typeof window === 'undefined') return 'en';
    return window.__locale || 'en';
});

const languageOptions = ref([]);
const rows = ref([]);
const loading = ref(true);
const error = ref('');
const searchQuery = ref('');
const searchDebounce = ref(null);
const currentPage = ref(1);
const perPage = ref(15);
const deleteModalOpen = ref(false);
const postToDelete = ref(null);
const deleting = ref(false);

const createPostHref = computed(() => {
    const q = new URLSearchParams({ lang: listLang.value || 'en' });
    return `/admin/blog/posts/create?${q.toString()}`;
});

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
                    href: `/admin/blog/posts/${tr.post_id}/edit`,
                    isCreate: false,
                    title: t('admin.content.edit_translation_title').replace(':lang', label),
                };
            }

            return {
                lcode: lang.lcode,
                href: `/admin/blog/posts/create?lang=${encodeURIComponent(lang.lcode)}&translation_group=${encodeURIComponent(gid)}`,
                isCreate: true,
                title: t('admin.content.translation_create_title').replace(':lang', label),
            };
        });
}

function setCsrf() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

function formatDate(value) {
    if (!value) return '—';
    try {
        const d = new Date(value);
        return d.toLocaleDateString(locale.value === 'ru' ? 'ru-RU' : 'en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    } catch {
        return value;
    }
}

const filteredRows = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return rows.value;
    return rows.value.filter((row) => {
        const title = (row.post?.title || '').toLowerCase();
        const slug = (row.post?.slug || '').toLowerCase();
        return title.includes(q) || slug.includes(q);
    });
});

const totalPages = computed(() => Math.max(1, Math.ceil(filteredRows.value.length / perPage.value)));

const pagedRows = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    return filteredRows.value.slice(start, start + perPage.value);
});

const pageFrom = computed(() => {
    if (filteredRows.value.length === 0) return 0;
    return (currentPage.value - 1) * perPage.value + 1;
});

const pageTo = computed(() => Math.min(filteredRows.value.length, currentPage.value * perPage.value));

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
        const { data } = await axios.get('/admin/blog/api/posts', {
            params: { lang: listLang.value },
            headers: { Accept: 'application/json' },
        });
        rows.value = Array.isArray(data) ? data : [];
        currentPage.value = 1;
    } catch (e) {
        error.value = e.response?.data?.message || e.message || t('admin.blog_posts.load_error');
    } finally {
        loading.value = false;
    }
}

function openDeleteModal(post) {
    postToDelete.value = post;
    deleteModalOpen.value = true;
}

function closeDeleteModal() {
    if (!deleting.value) {
        deleteModalOpen.value = false;
        postToDelete.value = null;
    }
}

async function confirmDelete() {
    if (!postToDelete.value) return;
    setCsrf();
    deleting.value = true;
    try {
        await axios.delete(`/admin/blog/api/posts/${postToDelete.value.id}`);
        closeDeleteModal();
        await load();
    } catch (e) {
        error.value = e.response?.data?.message || t('admin.blog_posts.load_error');
    } finally {
        deleting.value = false;
    }
}

onMounted(async () => {
    await loadLanguages();
    await load();
});
</script>
